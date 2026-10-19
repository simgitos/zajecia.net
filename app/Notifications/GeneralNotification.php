<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GeneralNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
        public string $type = 'info', // 'info', 'warning', 'success', 'danger'
        public ?string $url = null,
        public ?string $senderName = null,
        public ?int $schoolId = null,
        public ?string $actionText = null
    ) {}

    /**
     * Kanały powiadomień.
     * Zgodnie z architekturą: gotowe do włączenia kanałów 'mail' oraz 'sms'.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // W przyszłości: włączenie wysyłki e-mail w oparciu o preferencje użytkownika lub typ wiadomości
        // if (!empty($notifiable->email) && config('notifications.mail_enabled', false)) {
        //     $channels[] = 'mail';
        // }

        // W przyszłości: integracja z bramką SMS (np. SMSAPI, Twilio)
        // if (!empty($notifiable->phone) && config('notifications.sms_enabled', false)) {
        //     $channels[] = 'sms';
        // }

        return $channels;
    }

    /**
     * Reprezentacja dla bazy danych.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'type' => $this->type,
            'url' => $this->url,
            'sender_name' => $this->senderName,
            'school_id' => $this->schoolId,
            'action_text' => $this->actionText,
            'created_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Reprezentacja dla wiadomości e-mail (gotowa do użycia po włączeniu kanału 'mail').
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->title)
            ->greeting('Dzień dobry!')
            ->line($this->message);

        if ($this->url) {
            $mail->action($this->actionText ?? 'Przejdź do serwisu', url($this->url));
        }

        if ($this->senderName) {
            $mail->salutation("Pozdrawiamy,\n" . $this->senderName);
        }

        return $mail;
    }

    /**
     * Reprezentacja dla bramki SMS (architektura pod przyszłą rozbudowę).
     *
     * @return array<string, mixed>
     */
    public function toSms(object $notifiable): array
    {
        return [
            'to' => $notifiable->phone ?? null,
            'message' => $this->title . ': ' . $this->message,
        ];
    }
}
