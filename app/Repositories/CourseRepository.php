<?php

namespace App\Repositories;

use App\Models\Course;

class CourseRepository
{
    public function getAll()
    {
        $courses = Course::with('category')->latest()->paginate(10);
        $courses->getCollection()->transform(function ($course) {
            if ($course->image) {
                $course->image = asset('storage/' . $course->image);
            }
            return $course;
        });
        return $courses;
    }

    public function findById($id)
    {
        $courses = Course::with('category')->find($id);
        if ($courses && $courses->image) {
            $courses->image = asset('storage/' . $courses->image);
        }

        return $courses;
        
    }

    public function create(array $data)
    {
        return Course::create($data);
    }

    public function update($id, array $data)
    {
        $course = Course::find($id);
        if (!$course) {
            return null;
        }
        $course->update($data);
        return $course;
    }

    public function delete($id)
    {
        $course = Course::find($id);
        if (!$course) {
            return null;
        }
        return $course->delete();
    }
}