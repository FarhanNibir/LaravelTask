<?php

namespace App\Http\Controllers;
use App\Models\Content;
use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with(['modules', 'modules.contents'])->get();
        return view('courses.index', compact('courses'));
    }

    public function create()
    {
        return view('courses.create');
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('courses.edit', compact('course'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'feature_video' => 'nullable|file|mimes:mp4,mov,avi|max:51200',
            'modules' => 'required|array|min:1',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.contents' => 'required|array|min:1',
            'modules.*.contents.*.title' => 'required|string|max:255',
            'modules.*.contents.*.source_type' => 'required|in:YouTube,Vimeo',
            'modules.*.contents.*.video_url' => 'required|string|max:255',
            'modules.*.contents.*.video_length' => [
                'required',
                'string',
                'max:20',
                'regex:/^(\d{2}):(\d{2}):(\d{2})$/'
            ],
        ], [
            'modules.*.contents.*.video_length.regex' => 'The video length must be in HH:MM:SS format',
            'modules.required' => 'At least one module is required',
            'modules.*.contents.required' => 'Each module must have at least one content',
        ]);


        try {
            $featureVideoPath = null;
            if ($request->hasFile('feature_video')) {
                $featureVideoPath = $request->file('feature_video')->store('course_feature_videos', 'public');
            }

            DB::beginTransaction();

            $course = Course::create([
                'title' => $validatedData['title'],
                'feature_video' => $featureVideoPath,
            ]);


            foreach ($validatedData['modules'] as $moduleData) {
                $module = $course->modules()->create([
                    'title' => $moduleData['title'],
                    'course_id' => $course->id,
                ]);

                foreach ($moduleData['contents'] as $contentData) {
                    $content = $module->contents()->create([
                        'title' => $contentData['title'],
                        'module_id' => $module->id,
                        'video_source_type' => $contentData['source_type'],
                        'video_url' => $contentData['video_url'],
                        'video_length' => $contentData['video_length'],
                    ]);
//                    dd($content);
                }

            }

            DB::commit();

            return redirect()->route('courses.index')
                ->with('success', 'Course created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', 'Error creating course: ' . $e->getMessage());
        }
    }
    public function update(Request $request, Course $course)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'feature_video' => 'nullable|file|mimes:mp4,mov,avi|max:51200',
            'remove_feature_video' => 'nullable|boolean',
            'modules' => 'required|array|min:1',
            'modules.*.id' => 'nullable|integer|exists:modules,id',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.contents' => 'required|array|min:1',
            'modules.*.contents.*.id' => 'nullable|integer|exists:contents,id',
            'modules.*.contents.*.title' => 'required|string|max:255',
            'modules.*.contents.*.source_type' => 'required|in:YouTube,Vimeo',
            'modules.*.contents.*.video_url' => 'required|string|max:255',
            'modules.*.contents.*.video_length' => [
                'required',
                'string',
                'max:20',
                'regex:/^(\d{2}):(\d{2}):(\d{2})$/'
            ],
        ]);

        try {
            DB::beginTransaction();

            $featureVideoPath = $course->feature_video;
            if ($request->has('remove_feature_video') && $featureVideoPath) {
                Storage::disk('public')->delete($featureVideoPath);
                $featureVideoPath = null;
            }
            if ($request->hasFile('feature_video')) {
                if ($featureVideoPath) {
                    Storage::disk('public')->delete($featureVideoPath);
                }
                $featureVideoPath = $request->file('feature_video')->store('course_feature_videos', 'public');
            }

            $course->update([
                'title' => $validatedData['title'],
                'feature_video' => $featureVideoPath,
            ]);

            $existingModuleIds = $course->modules()->pluck('id')->toArray();
            $updatedModuleIds = [];

            foreach ($validatedData['modules'] as $moduleData) {
                $module = isset($moduleData['id'])
                    ? $course->modules()->findOrFail($moduleData['id'])
                    : new Module();

                $module->fill([
                    'title' => $moduleData['title'],
                    'course_id' => $course->id,
                ])->save();
                $updatedModuleIds[] = $module->id;

                $existingContentIds = isset($moduleData['id'])
                    ? $module->contents()->pluck('id')->toArray()
                    : [];
                $updatedContentIds = [];

                foreach ($moduleData['contents'] as $contentData) {
                    $content = isset($contentData['id'])
                        ? $module->contents()->findOrFail($contentData['id'])
                        : new Content();

                    $content->fill([
                        'title' => $contentData['title'],
                        'video_source_type' => $contentData['source_type'],
                        'video_url' => $contentData['video_url'],
                        'video_length' => $contentData['video_length'],
                        'module_id' => $module->id,
                    ])->save();
                    $updatedContentIds[] = $content->id;
                }

//                dd($existingContentIds);
                if (!empty($existingContentIds)) {
                    $contentsToDelete = array_diff($existingContentIds, $updatedContentIds);
                    if (!empty($contentsToDelete)) {
                        $module->contents()->whereIn('id', $contentsToDelete)->delete();
                    }
                }
            }

            if (!empty($existingModuleIds)) {
                $modulesToDelete = array_diff($existingModuleIds, $updatedModuleIds);
                if (!empty($modulesToDelete)) {
                    $course->modules()->whereIn('id', $modulesToDelete)->delete();
                }
            }

            DB::commit();

            return redirect()->route('courses.index')
                ->with('success', 'Course updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()
                ->with('error', 'Error updating course: ' . $e->getMessage());
        }
    }
    public function destroy($id)
    {
        $course = Course::findOrFail($id);

        foreach ($course->modules as $module) {
            $module->contents()->delete();
            $module->delete();
        }

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Course deleted successfully.');
    }

}

