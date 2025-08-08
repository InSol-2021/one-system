<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Exception;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {email? : Email address to send test email to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? $this->ask('Enter email address to send test email to');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Invalid email address provided.');
            return 1;
        }

        $this->info('Testing email configuration...');
        $this->info('SMTP Host: ' . config('mail.mailers.smtp.host'));
        $this->info('SMTP Port: ' . config('mail.mailers.smtp.port'));
        $this->info('SMTP Encryption: ' . config('mail.mailers.smtp.encryption'));
        $this->info('From Address: ' . config('mail.from.address'));
        $this->newLine();

        try {
            Mail::raw('This is a test email from Laravel CAS System to verify SMTP configuration is working properly.', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Laravel CAS - SMTP Configuration Test')
                        ->from(config('mail.from.address'), config('mail.from.name'));
            });

            $this->info('✅ Email sent successfully to: ' . $email);
            $this->info('Check your inbox (and spam folder) for the test email.');
            return 0;
            
        } catch (Exception $e) {
            $this->error('❌ Email sending failed!');
            $this->error('Error: ' . $e->getMessage());
            $this->newLine();
            
            // Provide helpful troubleshooting tips
            $this->warn('Troubleshooting Tips:');
            $this->line('1. Verify Gmail credentials in .env file');
            $this->line('2. Ensure you\'re using an App Password (not your regular Gmail password)');
            $this->line('3. Check that 2-Factor Authentication is enabled on your Gmail account');
            $this->line('4. Verify MAIL_ENCRYPTION=tls for port 587 or ssl for port 465');
            $this->line('5. Run: php artisan config:clear to refresh configuration');
            $this->newLine();
            $this->info('📖 See GMAIL_SMTP_SETUP_GUIDE.md for detailed setup instructions');
            
            return 1;
        }
    }
}
