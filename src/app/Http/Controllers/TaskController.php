<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\Task;
use App\Http\Requests\CreateTask;
use App\Http\Requests\EditTask;
use Illuminate\Support\Facades\Auth;


class TaskController extends Controller
{
    public function index(Folder $folder)
    {
        // ユーザーのフォルダを取得する
        $folders = Auth::user()->folders()->get();

        // 全てのフォルダを取得する
        // $folders = Folder::all();

        // 選ばれたフォルダを取得する
        // $current_folder = Folder::find($id);
        $tasks = $folder->tasks()->get();

        // 選ばれたフォルダに紐づくタスクを取得する
        // $tasks = Task::where('folder_id', $current_folder->id)->get();
        // $tasks = $current_folder->tasks()->get();
        // if (Auth::user()->id !== $folder->user_id){
        //     abort(403);
        // }

        return view('tasks/index', [
            'folders' => $folders,
            'current_folder_id' => $folder ->id,
            'tasks' => $tasks,
        ]);
    }

    public function showCreateForm(Folder $folder)
    {
        return view('tasks/create', [
            'folder_id' => $folder->id,
        ]);
    }

    public function create(Folder $folder, CreateTask $request)
    {
        // $current_folder = Folder::find($id);

        $task = new Task();
        $task->title = $request->title;
        $task->due_date = $request->due_date;

        // $current_folder->tasks()->save($task);
        $folder->tasks()->save($task);

        return redirect()->route('tasks.index', [
            'folder' => $folder->id,
        ]);
    }

    public function showEditForm(Folder $folder, Task $task)
    {
        // $task = Task::find($task_id);
        // if ($folder->id !== $task->folder_id) {
        //     abort(404);
        // }
        $this->checkRelation($folder, $task);

        return view('tasks/edit', [
            'task' => $task,
        ]);
    }

    public function edit(Folder $folder, Task $task, EditTask $request)
    {
        // if ($folder->id !== $task->folder_id) {
        //     abort(404);
        // }
        $this->checkRelation($folder, $task);
        // 1
        // $task = Task::find($task_id);

        // 2
        $task->title = $request->title;
        $task->status = $request->status;
        $task->due_date = $request->due_date;
        $task->save();

        // 3
        return redirect()->route('tasks.index', [
            // 'id' => $task->folder_id,
            'folder' => $task->folder_id,
            'task' => $task->id,
        ]);
    }

    private function checkRelation(Folder $folder, Task $task)
    {
        if ($folder->id !== $task->folder_id) {
            abort(404);
        }
    }
}
