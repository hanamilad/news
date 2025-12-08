<?php

namespace App\GraphQL\Mutations;

use App\Http\Requests\Task\TaskRequest;
use App\Services\Task\TaskService;
use Illuminate\Validation\ValidationException;

class TaskMutator
{
    public function __construct(protected TaskService $service) {}

    public function create($_, array $args)
    {
        $input = $args['input'] ?? [];
        $validator = validator($input, (new TaskRequest)->rules());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->create($input);
    }

    public function update($_, array $args)
    {
        $id = (int) $args['id'];
        $input = $args['input'] ?? [];
        $input['id'] = $id;
        $request = new TaskRequest;
        $request->merge($input);
        $validator = validator($input, $request->rules(), $request->messages());
        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $this->service->update($id, $input);
    }

    public function delete($_, array $args)
    {
        $id = (int) $args['id'];

        return $this->service->delete($id);
    }

    public function assignUsers($_, array $args)
    {
        $taskId = (int) $args['task_id'];
        $userIds = $args['user_ids'] ?? [];

        return $this->service->assignUsers($taskId, $userIds);
    }

    public function unassignUsers($_, array $args)
    {
        $taskId = (int) $args['task_id'];
        $userIds = $args['user_ids'] ?? [];

        return $this->service->unassignUsers($taskId, $userIds);
    }

    public function setStatus($_, array $args)
    {
        $taskId = (int) $args['task_id'];
        $status = (string) $args['status'];

        return $this->service->setStatus($taskId, $status);
    }

    public function finishingTasks($_, array $args)
    {
        $items = $args['items'] ?? [];

        return $this->service->finishTasks($items);
    }
}
