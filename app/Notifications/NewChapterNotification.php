<?php

namespace App\Notifications;

use App\Models\Chapter;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewChapterNotification extends Notification
{
    use Queueable;

    public function __construct(public Chapter $chapter)
    {
        $this->chapter->loadMissing('manga');
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $manga = $this->chapter->manga;

        return (new MailMessage)
            ->subject(sprintf('%s için yeni bölüm yayınlandı', $manga->title))
            ->greeting('Merhaba!')
            ->line(sprintf('%s serisinin %d numaralı bölümü yayınlandı.', $manga->title, $this->chapter->number))
            ->action('Bölümü Oku', route('chapters.show', [$manga, $this->chapter->number]))
            ->line('Takipte kaldığınız için teşekkürler!');
    }
}
