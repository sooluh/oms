<?php

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('app.name', 'Oh My Student');
        $this->migrator->add('app.holder', 'SMK Negeri 404 Lorem Ipsum');
        $this->migrator->add('app.region', 'Pemalang');

        $this->migrator->add('app.address', 'Jl. Not Found No. 404, Pemalang, Jawa Tengah, Indonesia');
        $this->migrator->add('app.email', json_encode([['email' => 'admin@sekolah.ac.id']]));
        $this->migrator->add('app.phone', json_encode([['number' => '6285123456789', 'note' => 'WhatsApp']]));

        $this->migrator->add('app.social', json_encode([
            ['name' => 'Twitter', 'icon' => 'twitter', 'value' => ''],
            ['name' => 'Facebook', 'icon' => 'facebook-f', 'value' => ''],
            ['name' => 'Instagram', 'icon' => 'instagram', 'value' => ''],
            ['name' => 'YouTube', 'icon' => 'youtube', 'value' => ''],
        ]));
    }

    public function down(): void
    {
        $this->migrator->deleteIfExists('app.name');
        $this->migrator->deleteIfExists('app.holder');
        $this->migrator->deleteIfExists('app.region');

        $this->migrator->deleteIfExists('app.address');
        $this->migrator->deleteIfExists('app.email');
        $this->migrator->deleteIfExists('app.phone');

        $this->migrator->deleteIfExists('app.social');
    }
};
