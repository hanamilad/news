<?php

namespace App\GraphQL\Mutations;

use App\Http\Requests\Category\CategoryRequest;
use App\Services\Category\CategoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CategoryMutator
{
    public function __construct(protected CategoryService $service) {}

    public function create($_, array $args)
    {
        $inputs = $args['input'] ?? [];
        $results = [];

        DB::beginTransaction();

        try {
            foreach ($inputs as $input) {
                $validator = validator($input, CategoryRequest::creationRules(), (new CategoryRequest)->messages());

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }

                $results[] = $this->service->create($input);
            }

            DB::commit();

            return $results;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function update($_, array $args)
    {
        $inputs = $args['input'] ?? [];
        $updated = [];

        DB::beginTransaction();

        try {
            foreach ($inputs as $item) {

                if (! isset($item['id']) || ! isset($item['data'])) {
                    throw new \Exception('id and data fields are required for each update item');
                }

                $id = (int) $item['id'];
                $data = $item['data'];

                $validator = validator($data, CategoryRequest::updateRules(), (new CategoryRequest)->messages());

                if ($validator->fails()) {
                    throw new ValidationException($validator);
                }

                $updated[] = $this->service->update($id, $data);
            }

            DB::commit();

            return $updated;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function delete($_, array $args)
    {
        $id = (int) $args['id'];

        return $this->service->delete($id);
    }
}
