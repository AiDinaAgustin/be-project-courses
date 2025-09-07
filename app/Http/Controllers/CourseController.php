<?php

namespace App\Http\Controllers;

use App\Imports\CourseImport;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Services\CourseService;
use App\Exports\CoursesExport;
use Maatwebsite\Excel\Facades\Excel;


class CourseController extends Controller
{
    protected $courseService;

    public function __construct(CourseService $courseService)
    {
        $this->courseService = $courseService;
    }

    public function index()
    {
        $course = $this->courseService->getAllCourses();
        if(count($course) == 0) {
            return response()->json(['message' => 'No courses found'], 404);
        } else {
            return response()->json([
                'message' => 'Courses retrieved successfully',
                'status' => 200,
                'data' => $course->items(),
                'pagination' => [
                    'current_page' => $course->currentPage(),
                    'last_page' => $course->lastPage(),
                    'per_page' => $course->perPage(),
                    'total' => $course->total()
                ]
            ]);
        }
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'image' => 'nullable|image'
        ]);

        if ( $request->hasFile('image')) {
            $imagePath = $request->file('image')->store('courses', 'public');
            $data['image'] = $imagePath;
        }

        $course = $this->courseService->createCourse($data);
        return response()->json([
            'message' => 'Course created successfully',
            'status' => 201,
            'data' => $course
        ], 201);
    }

    public function show($id)
    {
        $course = $this->courseService->getCourseById($id);

        if(!$course) {
            return response()->json([
                'message' => 'Course not found',
                'status' => 404
            ], 404);
        } else {
            return response()->json([
                'message' => 'Course retrieved successfully',
                'status' => 200,
                'data' => $course
            ]);
        }
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'image' => 'sometimes|nullable|image'
        ]);

        if ($request->hasFile('image')) {
            $currentCourse = $this->courseService->getCourseById($id);
            if ($currentCourse && $currentCourse->image) {
                Storage::disk('public')->delete($currentCourse->image);
            }
            $imagePath = $request->file('image')->store('courses', 'public');
            $data['image'] = $imagePath;
        }

        $course = $this->courseService->updateCourse($id, $data);

        if(!$course) {
            return response()->json([
                'message' => 'Course not found',
                'status' => 404
            ], 404);
        } else {
            return response()->json([
                'message' => 'Course updated successfully',
                'status' => 200,
                'data' => $course
            ]);
        }
    }

    public function destroy($id)
    {
        $course = $this->courseService->deleteCourse($id);

        if(!$course) {
            return response()->json([
                'message' => 'Course not found',
                'status' => 404
            ], 404);
        } else {
            return response()->json([
                'message' => 'Course deleted successfully',
                'status' => 200
            ]);
        }
    }

    public function export()
    {
        return Excel::download(new CoursesExport, 'courses.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv'
        ]);
    
        $file = $request->file('file');
        $nama_file = uniqid() . '_' . $file->getClientOriginalName();
    
        // Simpan file ke storage/app/public/file_course
        $path = $file->storeAs('file_course', $nama_file, 'public');
    
        // Import data dari file yang sudah diupload
        Excel::import(new CourseImport, storage_path('app/public/' . $path));
    
        return response()->json([
            'message' => 'Courses imported successfully',
            'status' => 200
        ], 200);
    }
}
