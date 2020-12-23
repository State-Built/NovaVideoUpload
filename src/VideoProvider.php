<?php


namespace App\Film;


interface VideoProvider
{
    /**
     * @param Film   $film
     * @param string $localPath
     * @return int|string The ID from provider
     */
    public function upload(Film $film, string $localPath) : int|string;
}