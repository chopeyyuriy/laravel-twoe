<?php

namespace App\Services;

class Cours
{
    public function getCourses()
    {
		$courses = \App\Courses::get();
		return ['courses' => $courses];
    }
}