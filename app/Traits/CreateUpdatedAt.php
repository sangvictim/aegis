<?php

namespace App\Traits;

trait CreateUpdatedAt
{
    /**
     * Get the user's created_at in datetime format.
     */
    public function getCreatedAtAttribute(): string
    {
        return (new \DateTime($this->attributes['created_at']))->format('Y-m-d H:i:s');
    }

    /**
     * Get the user's updated_at in datetime format.
     */
    public function getUpdatedAtAttribute(): string
    {
        return (new \DateTime($this->attributes['updated_at']))->format('Y-m-d H:i:s');
    }
}
