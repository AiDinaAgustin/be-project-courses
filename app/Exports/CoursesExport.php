<?php

namespace App\Exports;

use App\Models\Course;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CoursesExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Course::with('category')
            ->get()
            ->map(function ($course) {
                return [
                    'ID' => $course->id,
                    'Title' => $course->title,
                    'Description' => $course->description,
                    'Category' => $course->category ? $course->category->name : 'Uncategorized',
                ];
            });
    }

    public function headings(): array{
        return [
            'ID',
            'Title',
            'Description',
            'Category',
        ];
    }
}
