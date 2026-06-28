<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClassEnrollmentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'student_id' => $this->student_id,
            'class_id' => $this->class_id,
            'academic_year_id' => $this->academic_year_id,
            'status' => $this->status,

            // Relasi (Eager Loading) agar frontend mudah menampilkan data
            'student_name' => $this->whenLoaded('student', function () {
                return $this->student->name;
            }),
            'student_nis' => $this->whenLoaded('student', function () {
                return $this->student->nis;
            }),
            'class_name' => $this->whenLoaded('classRoom', function () {
                return $this->classRoom->name;
            }),
            'academic_year_name' => $this->whenLoaded('academicYear', function () {
                return $this->academicYear->name;
            }),

            'created_at' => $this->created_at,
        ];
    }
}
