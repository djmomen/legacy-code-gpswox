<?php

namespace Tobuli\Helpers\BroadcastMessage;

class Message
{
    private $channels;
    private $receiversCriteria;
    private $title;
    private $content;

    public function __construct(array $channels, array $receiversCriteria, string $title, string $content)
    {
        $this->channels = $channels;
        $this->receiversCriteria = $receiversCriteria;
        $this->title = $title;
        $this->content = $content;
    }

    public function getChannels(): array
    {
        return $this->channels;
    }

    public function getReceiversCriteria(): array
    {
        return $this->receiversCriteria;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
