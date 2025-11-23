<?php

namespace App\GraphQL\Mutations;

use App\Services\Category\CategoryService;
use App\Http\Requests\Category\CategoryRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;


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
                // Validate each category separately
                $validator = validator($input, (new CategoryRequest())->rules());
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
                if (!isset($item['id']) || !isset($item['data'])) {
                    throw new \Exception("id and data fields are required for each update item");
                }

                $id = (int) $item['id'];
                $data = $item['data'];

                $validator = validator($data, (new CategoryRequest())->rules());
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
        $id = (int)$args['id'];
        return $this->service->delete($id);
    }
}
