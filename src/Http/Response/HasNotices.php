<?php

namespace Engency\Http\Response;

use Illuminate\Support\MessageBag;

trait HasNotices
{
    private ?MessageBag $bag;

    /**
     * @param MessageBag $bag
     */
    public function addBag(MessageBag $bag)
    {
        $this->bag->merge($bag);
    }

    /**
     * @return MessageBag
     */
    public function getBag() : MessageBag
    {
        return $this->bag;
    }

    /**
     * @param string $message
     * @param string $type
     * @return $this
     */
    public function addNotice(string $message, string $type = Notice::NOTICE_ERROR)
    {
        $this->bag->add('notice-' . $type, $message);

        return $this;
    }

    /**
     * @param string $field
     * @param string $message
     * @return $this
     */
    public function addNoticeOnField(string $field, string $message)
    {
        $this->bag->add($field, $message);

        return $this;
    }

}