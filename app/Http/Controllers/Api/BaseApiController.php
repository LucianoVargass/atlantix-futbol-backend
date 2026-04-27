<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

abstract class BaseApiController extends Controller
{
    /**
     * The model class handled by the controller.
     *
     * @var class-string<Model>
     */
    protected string $modelClass;

    protected function rules(bool $isUpdate = false): array
    {
        return [];
    }

    protected function validateRequest(Request $request, bool $isUpdate = false): array
    {
        $rules = $this->rules($isUpdate);
        if ($isUpdate && !empty($rules)) {
            foreach ($rules as $field => $rule) {
                if (is_string($rule)) {
                    if (!str_contains($rule, 'sometimes')) {
                        $rules[$field] = 'sometimes|' . $rule;
                    }
                } elseif (is_array($rule) && !in_array('sometimes', $rule, true)) {
                    array_unshift($rule, 'sometimes');
                    $rules[$field] = $rule;
                }
            }
        }

        return $request->validate($rules);
    }

    protected function query(Request $request): Builder
    {
        $modelClass = $this->modelClass;
        $query = $modelClass::query();

        if ($request->filled('with')) {
            $with = array_filter(array_map('trim', explode(',', $request->string('with')->value())));
            if (!empty($with)) {
                $query->with($with);
            }
        }

        return $query;
    }

    public function index(Request $request)
    {
        $perPage = $request->integer('per_page');
        $query = $this->query($request);

        if ($perPage > 0) {
            return response()->json($query->paginate($perPage));
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $modelClass = $this->modelClass;
        $data = $this->validateRequest($request, false);
        $record = $modelClass::create($data);

        return response()->json($record, 201);
    }

    public function show(Request $request, string $id)
    {
        $record = $this->query($request)->findOrFail($id);

        return response()->json($record);
    }

    public function update(Request $request, string $id)
    {
        $modelClass = $this->modelClass;
        $record = $modelClass::findOrFail($id);
        $data = $this->validateRequest($request, true);
        $record->fill($data);
        $record->save();

        return response()->json($record);
    }

    public function destroy(string $id)
    {
        $modelClass = $this->modelClass;
        $record = $modelClass::findOrFail($id);
        $record->delete();

        return response()->json(['deleted' => true]);
    }
}
