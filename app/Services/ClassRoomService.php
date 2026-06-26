<?php

namespace App\Services;

use App\Models\ClassRoom;

class ClassRoomService
{
    public function getAll()
    {
        return ClassRoom::orderBy('name', 'asc')->get();
    }

    public function create(array $data)
    {
        return ClassRoom::create($data);
    }

    public function update(ClassRoom $classRoom, array $data)
    {
        $classRoom->update($data);
        return $classRoom;
    }

    public function delete(ClassRoom $classRoom)
    {
        $classRoom->delete();
        return true;
    }
}
