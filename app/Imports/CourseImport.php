<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Course;
use Maatwebsite\Excel\Concerns\ToModel;

class CourseImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $category = Category::where('name', $row[3])->first();
        return new Course([
            'title' => $row[1],
            'description'=> $row[2],
            'category_id'=> $category ? $category->id : null,
        ]);
    }
}
