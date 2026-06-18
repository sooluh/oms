<?php

namespace Database\Seeders;

use App\Enums\FieldType;
use App\Enums\SearchMode;
use App\Models\Service;
use App\Models\ServiceRecord;
use Illuminate\Database\Seeder;

class DemoServiceSeeder extends Seeder
{
    public function run(): void
    {
        $admission = Service::create([
            'name' => 'Admission',
            'slug' => 'admission',
            'icon' => 'phosphor-student-bold',
            'description' => 'Look up your new student admission status, including your assigned session, room, and document verification schedule.',
            'is_active' => true,
            'start_date' => null,
            'end_date' => null,
            'search_mode' => SearchMode::Or,
            'max_attempt' => 10,
            'attempt_duration' => 5,
            'sort_order' => 1,
        ]);

        $admissionFields = [
            [
                'name' => 'student_number',
                'label' => 'Student Number',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => true,
                'identifier_order' => 1,
                'sort_order' => 1,
            ],
            [
                'name' => 'sequence_number',
                'label' => 'Sequence Number',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 2,
            ],
            [
                'name' => 'description',
                'label' => 'Description',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 3,
            ],
            [
                'name' => 'full_name',
                'label' => 'Full Name',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 4,
            ],
            [
                'name' => 'gender',
                'label' => 'Gender',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 5,
            ],
            [
                'name' => 'phone_number',
                'label' => 'Phone Number',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 6,
            ],
            [
                'name' => 'origin_school',
                'label' => 'Origin School',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 7,
            ],
            [
                'name' => 'admission_track',
                'label' => 'Admission Track',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 8,
            ],
            [
                'name' => 'session',
                'label' => 'Session',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 9,
            ],
            [
                'name' => 'room',
                'label' => 'Room',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 10,
            ],
            [
                'name' => 'verification_date',
                'label' => 'Verification Date',
                'type' => FieldType::Date,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 11,
            ],
        ];

        $admission->fields()->createMany($admissionFields);

        $admissionRecords = [
            [
                'student_number' => '3234567890',
                'sequence_number' => '001',
                'description' => 'Ruang 1 Kursi 1',
                'full_name' => 'Aulia Rahmawati',
                'gender' => 'Female',
                'phone_number' => '081234567890',
                'origin_school' => 'SMP Negeri 1 Jakarta',
                'admission_track' => 'Prestasi',
                'session' => 'Pagi (07.45 - 12.00)',
                'room' => 'Ruang 1',
                'verification_date' => '2026-06-04',
            ],
            [
                'student_number' => '3234567891',
                'sequence_number' => '002',
                'description' => 'Ruang 1 Kursi 2',
                'full_name' => 'Fajar Nugroho',
                'gender' => 'Male',
                'phone_number' => '082345678901',
                'origin_school' => 'SMP Negeri 3 Bandung',
                'admission_track' => 'Domisili',
                'session' => 'Pagi (07.45 - 12.00)',
                'room' => 'Ruang 1',
                'verification_date' => '2026-06-04',
            ],
            [
                'student_number' => '3234567892',
                'sequence_number' => '003',
                'description' => 'Ruang 2 Kursi 1',
                'full_name' => 'Mega Lestari',
                'gender' => 'Female',
                'phone_number' => '083456789012',
                'origin_school' => 'MTs Negeri 2 Surabaya',
                'admission_track' => 'Afirmasi',
                'session' => 'Siang (12.30 - 17.00)',
                'room' => 'Ruang 2',
                'verification_date' => '2026-06-05',
            ],
            [
                'student_number' => '3234567893',
                'sequence_number' => '004',
                'description' => 'Ruang 2 Kursi 2',
                'full_name' => 'Bagas Setiawan',
                'gender' => 'Male',
                'phone_number' => '084567890123',
                'origin_school' => 'SMP Swasta Al-Azhar Yogyakarta',
                'admission_track' => 'Mutasi',
                'session' => 'Siang (12.30 - 17.00)',
                'room' => 'Ruang 2',
                'verification_date' => '2026-06-05',
            ],
            [
                'student_number' => '3234567894',
                'sequence_number' => '005',
                'description' => 'Ruang 3 Kursi 1',
                'full_name' => 'Nadia Kusuma',
                'gender' => 'Female',
                'phone_number' => '085678901234',
                'origin_school' => 'SMP Negeri 5 Semarang',
                'admission_track' => 'Prestasi',
                'session' => 'Pagi (07.45 - 12.00)',
                'room' => 'Ruang 3',
                'verification_date' => '2026-06-04',
            ],
        ];

        foreach ($admissionRecords as $data) {
            ServiceRecord::create([
                'service_id' => $admission->id,
                'identifier_1' => $data['student_number'],
                'data' => $data,
            ]);
        }

        $nisn = Service::create([
            'name' => 'Student ID Lookup',
            'slug' => 'student-id-lookup',
            'icon' => 'phosphor-magnifying-glass-bold',
            'description' => 'Check your National Student Identification Number (NISN) and personal data registered with the Ministry of Education.',
            'is_active' => true,
            'start_date' => null,
            'end_date' => null,
            'search_mode' => SearchMode::And,
            'max_attempt' => 10,
            'attempt_duration' => 5,
            'sort_order' => 2,
        ]);

        $nisnFields = [
            [
                'name' => 'student_number',
                'label' => 'Student Number',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 1,
            ],
            [
                'name' => 'full_name',
                'label' => 'Full Name',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => true,
                'identifier_order' => 1,
                'sort_order' => 2,
            ],
            [
                'name' => 'pob',
                'label' => 'Place of Birth',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => true,
                'identifier_order' => 2,
                'sort_order' => 3,
            ],
            [
                'name' => 'dob',
                'label' => 'Date of Birth',
                'type' => FieldType::Date,
                'required' => true,
                'visible' => true,
                'is_identifier' => true,
                'identifier_order' => 3,
                'sort_order' => 4,
            ],
            [
                'name' => 'gender',
                'label' => 'Gender',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 5,
            ],
            [
                'name' => 'mother_name',
                'label' => "Mother's Name",
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => true,
                'identifier_order' => 4,
                'sort_order' => 6,
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => FieldType::Boolean,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 7,
                'true_label' => 'Active',
                'false_label' => 'Inactive',
            ],
        ];

        $nisn->fields()->createMany($nisnFields);

        $nisnRecords = [
            [
                'student_number' => '1234567890',
                'full_name' => 'Ahmad Fauzi',
                'pob' => 'Jakarta',
                'dob' => '2005-03-15',
                'gender' => 'Male',
                'mother_name' => 'Siti Aisyah',
                'status' => '1',
            ],
            [
                'student_number' => '1234567891',
                'full_name' => 'Siti Nurhaliza',
                'pob' => 'Bandung',
                'dob' => '2006-07-22',
                'gender' => 'Female',
                'mother_name' => 'Fatimah Zahra',
                'status' => '1',
            ],
            [
                'student_number' => '1234567892',
                'full_name' => 'Budi Santoso',
                'pob' => 'Surabaya',
                'dob' => '2005-11-08',
                'gender' => 'Male',
                'mother_name' => 'Dewi Sartika',
                'status' => '1',
            ],
            [
                'student_number' => '1234567893',
                'full_name' => 'Rina Amelia',
                'pob' => 'Yogyakarta',
                'dob' => '2006-01-30',
                'gender' => 'Female',
                'mother_name' => 'Kartini Susilo',
                'status' => '0',
            ],
            [
                'student_number' => '1234567894',
                'full_name' => 'Doni Prasetyo',
                'pob' => 'Semarang',
                'dob' => '2005-05-12',
                'gender' => 'Male',
                'mother_name' => 'Sri Wahyuni',
                'status' => '1',
            ],
        ];

        foreach ($nisnRecords as $data) {
            $identifiers = [
                'identifier_1' => $data['full_name'],
                'identifier_2' => $data['pob'],
                'identifier_3' => $data['dob'],
                'identifier_4' => $data['mother_name'],
            ];

            ServiceRecord::create([
                'service_id' => $nisn->id,
                ...$identifiers,
                'data' => $data,
            ]);
        }

        $lulus = Service::create([
            'name' => 'Graduation Status',
            'slug' => 'graduation-status',
            'icon' => 'phosphor-graduation-cap-bold',
            'description' => 'Verify your graduation status and download official graduation certificates and supporting documents.',
            'is_active' => true,
            'start_date' => null,
            'end_date' => null,
            'search_mode' => SearchMode::Or,
            'max_attempt' => 10,
            'attempt_duration' => 5,
            'sort_order' => 3,
        ]);

        $lulusFields = [
            [
                'name' => 'student_number',
                'label' => 'Student Number',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => true,
                'identifier_order' => 1,
                'sort_order' => 1,
            ],
            [
                'name' => 'full_name',
                'label' => 'Full Name',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 2,
            ],
            [
                'name' => 'pob',
                'label' => 'Place of Birth',
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 3,
            ],
            [
                'name' => 'dob',
                'label' => 'Date of Birth',
                'type' => FieldType::Date,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 4,
            ],
            [
                'name' => 'father_name',
                'label' => "Father's Name",
                'type' => FieldType::Text,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 5,
            ],
            [
                'name' => 'status',
                'label' => 'Status',
                'type' => FieldType::Boolean,
                'required' => true,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 6,
                'true_label' => 'Passed',
                'false_label' => 'Failed',
            ],
            [
                'name' => 'document_1',
                'label' => 'Graduation Certificate',
                'type' => FieldType::Link,
                'required' => false,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 7,
            ],
            [
                'name' => 'document_2',
                'label' => 'Transcript',
                'type' => FieldType::Link,
                'required' => false,
                'visible' => true,
                'is_identifier' => false,
                'identifier_order' => null,
                'sort_order' => 8,
            ],
        ];

        $lulus->fields()->createMany($lulusFields);

        $lulusRecords = [
            [
                'student_number' => '2234567890',
                'full_name' => 'Rizky Pratama',
                'pob' => 'Medan',
                'dob' => '2004-08-20',
                'father_name' => 'Hasan Basri',
                'status' => '1',
                'document_1' => 'https://example.com/certificates/001',
                'document_2' => 'https://example.com/transcripts/001',
            ],
            [
                'student_number' => '2234567891',
                'full_name' => 'Diana Permata',
                'pob' => 'Palembang',
                'dob' => '2005-02-14',
                'father_name' => 'Ahmad Dahlan',
                'status' => '1',
                'document_1' => 'https://example.com/certificates/002',
                'document_2' => 'https://example.com/transcripts/002',
            ],
            [
                'student_number' => '2234567892',
                'full_name' => 'Agus Wijaya',
                'pob' => 'Makassar',
                'dob' => '2004-11-05',
                'father_name' => 'Suparno',
                'status' => '0',
                'document_1' => null,
                'document_2' => null,
            ],
            [
                'student_number' => '2234567893',
                'full_name' => 'Putri Ayu',
                'pob' => 'Denpasar',
                'dob' => '2005-06-18',
                'father_name' => 'Wayan Sujana',
                'status' => '1',
                'document_1' => 'https://example.com/certificates/004',
                'document_2' => 'https://example.com/transcripts/004',
            ],
            [
                'student_number' => '2234567894',
                'full_name' => 'Hendra Gunawan',
                'pob' => 'Banjarmasin',
                'dob' => '2004-09-28',
                'father_name' => 'Mulyadi',
                'status' => '1',
                'document_1' => 'https://example.com/certificates/005',
                'document_2' => 'https://example.com/transcripts/005',
            ],
        ];

        foreach ($lulusRecords as $data) {
            ServiceRecord::create([
                'service_id' => $lulus->id,
                'identifier_1' => $data['student_number'],
                'data' => $data,
            ]);
        }
    }
}
