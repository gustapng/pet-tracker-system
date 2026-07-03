<?php

namespace App\Console\Commands;

use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SimulatePetHardware extends Command
{
    protected $signature = 'pet:simulate-hardware';
    
    protected $description = 'Simula a coleira do pet enviando coordenadas GPS HTTP para o Hyperf';

    public function handle()
    {
        $lat = -23.588333;
        $lng = -46.658890;
        $battery = 100;

        $this->info("🐶 Simulador de Hardware (Coleira) Iniciado!");
        $this->info("Enviando dados para o Hyperf a cada 30 segundos. Pressione CTRL+C para desligar.\n");

        while (true) {
            $lat += mt_rand(-200, 200) / 1000000;
            $lng += mt_rand(-200, 200) / 1000000;
            
            $battery = max(1, $battery - 0.5);
            $speed = rand(2, 12);

            try {
                $response = Http::post('http://tracker-api:9501/api/location/update', [
                    'pet_id' => 1,
                    'latitude' => $lat,
                    'longitude' => $lng,
                    'speed' => $speed,
                    'battery_level' => $battery,
                    'timestamp' => now()->timestamp
                ]);

                if ($response->successful()) {
                    $this->line(" [>] Pacote enviado! Lat: {$lat} | Lng: {$lng} | Bateria: {$battery}%");
                } else {
                    $this->error(" [!] Falha ao enviar para o Hyperf. Status: " . $response->status());
                }

            } catch (\Exception $e) {
                $this->error(" [!] Erro de conexão com o Hyperf: " . $e->getMessage());
            }

            sleep(20);
        }
    }
}
