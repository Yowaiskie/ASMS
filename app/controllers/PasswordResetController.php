<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Repositories\UserRepository;
use App\Core\Database;

class PasswordResetController extends Controller {
    private $userRepo;
    private $db;

    public function __construct() {
        $this->userRepo = new UserRepository();
        $this->db = Database::getInstance();
    }

    // Step 1: Show Email Entry Form
    public function forgotPassword() {
        if (isset($_SESSION['user_id'])) {
            redirect('dashboard');
        }
        require_once '../app/views/auth/forgot_password.php';
    }

    // Step 2: Process Email and Send OTP
    public function sendOtp() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = trim($_POST['email']);

            if (empty($email)) {
                $error = "Please enter your email address.";
                require_once '../app/views/auth/forgot_password.php';
                return;
            }

            // Find user by email
            $user = $this->userRepo->findByEmail($email);

            if ($user) {
                $otp = sprintf("%06d", mt_rand(1, 999999));
                $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                $this->db->query("DELETE FROM password_resets WHERE email = :email");
                $this->db->bind(':email', $email);
                $this->db->execute();

                $this->db->query("INSERT INTO password_resets (email, otp, expires_at) VALUES (:email, :otp, :expires)");
                $this->db->bind(':email', $email);
                $this->db->bind(':otp', $otp);
                $this->db->bind(':expires', $expires_at);
                
                if ($this->db->execute()) {
                    require_once '../app/helpers/mail_helper.php';
                    $subject = "ASMS - Password Reset OTP";
                    $title = "Your Password Reset OTP";
                    $message = "Your 6-digit OTP for password reset is: <br><br> <b style='font-size: 24px; letter-spacing: 5px; color: #1e63d4;'>$otp</b> <br><br> This code will expire in 15 minutes.";
                    
                    if (sendEmailNotification($email, $subject, $title, $message, 'Login Now', URLROOT . '/login')) {
                        $_SESSION['reset_email'] = $email;
                        unset($_SESSION['otp_verified']); // Reset verification status
                        setFlash('msg_success', 'An OTP has been sent to your email.');
                        redirect('verify-otp');
                    } else {
                        $error = "Failed to send email. Please try again later.";
                        require_once '../app/views/auth/forgot_password.php';
                    }
                } else {
                    $error = "Something went wrong. Please try again.";
                    require_once '../app/views/auth/forgot_password.php';
                }
            } else {
                $error = "No account found with that email address.";
                require_once '../app/views/auth/forgot_password.php';
            }
        }
    }

    // Step 3: Show OTP Verification Form
    public function verifyOtpForm() {
        if (!isset($_SESSION['reset_email'])) {
            redirect('forgot-password');
        }
        require_once '../app/views/auth/verify_otp.php';
    }

    // Step 4: Process OTP Verification
    public function verifyOtp() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'] ?? ($_SESSION['reset_email'] ?? '');
            $otp = trim($_POST['otp']);

            error_log("Verifying OTP: Email[$email], OTP[$otp]");

            if (empty($email)) {
                error_log("OTP Error: Email is empty.");
                redirect('forgot-password');
                return;
            }
            
            // Re-sync session just in case
            $_SESSION['reset_email'] = $email;

            if (empty($otp)) {
                $error = "Please enter the 6-digit OTP.";
                require_once '../app/views/auth/verify_otp.php';
                return;
            }

            // Verify OTP
            $this->db->query("SELECT * FROM password_resets WHERE email = :email AND otp = :otp AND expires_at > NOW()");
            $this->db->bind(':email', $email);
            $this->db->bind(':otp', $otp);
            $reset = $this->db->single();

            if ($reset) {
                error_log("OTP Success: Found reset record ID " . $reset->id);
                $_SESSION['otp_verified'] = true;
                $_SESSION['reset_otp'] = $otp; // Keep for the final update if needed
                redirect('reset-password');
            } else {
                error_log("OTP Fail: No matching or unexpired record found.");
                $error = "Invalid or expired OTP.";
                require_once '../app/views/auth/verify_otp.php';
            }
        }
    }

    // Step 5: Show Reset Password Form (Only if OTP verified)
    public function resetPasswordForm() {
        if (!isset($_SESSION['reset_email']) || !isset($_SESSION['otp_verified'])) {
            redirect('forgot-password');
        }
        require_once '../app/views/auth/reset_password.php';
    }

    // Step 6: Process Final Password Update
    public function updatePassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_SESSION['reset_email'] ?? '';
            $otp = $_SESSION['reset_otp'] ?? '';
            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['confirm_password']);

            if (!isset($_SESSION['otp_verified']) || empty($email)) {
                redirect('forgot-password');
                return;
            }

            if (empty($password) || empty($confirmPassword)) {
                $error = "Passwords are required.";
                require_once '../app/views/auth/reset_password.php';
                return;
            }

            if ($password !== $confirmPassword) {
                $error = "Passwords do not match.";
                require_once '../app/views/auth/reset_password.php';
                return;
            }

            // Find user
            $user = $this->userRepo->findByEmail($email);
            if ($user) {
                // Double check OTP validity one last time for security
                $this->db->query("SELECT * FROM password_resets WHERE email = :email AND otp = :otp AND expires_at > NOW()");
                $this->db->bind(':email', $email);
                $this->db->bind(':otp', $otp);
                if (!$this->db->single()) {
                    setFlash('msg_error', 'Session expired. Please start over.');
                    redirect('forgot-password');
                    return;
                }

                $newPassword = password_hash($password, PASSWORD_DEFAULT);
                if ($this->userRepo->update($user->id, ['password' => $newPassword, 'force_password_reset' => 0])) {
                    // Cleanup
                    $this->db->query("DELETE FROM password_resets WHERE email = :email");
                    $this->db->bind(':email', $email);
                    $this->db->execute();

                    unset($_SESSION['reset_email']);
                    unset($_SESSION['otp_verified']);
                    unset($_SESSION['reset_otp']);

                    setFlash('msg_success', 'Password reset successful!');
                    redirect('reset-success');
                } else {
                    $error = "Failed to update password.";
                    require_once '../app/views/auth/reset_password.php';
                }
            } else {
                $error = "User not found.";
                require_once '../app/views/auth/reset_password.php';
            }
        }
    }

    // Step 7: Show Success Page with Countdown
    public function resetSuccess() {
        require_once '../app/views/auth/reset_success.php';
    }
}
