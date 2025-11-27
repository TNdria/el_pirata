<?php

namespace App\helpers;
use Illuminate\Support\Facades\Storage;
use App\Services\ImageCompressionService;


class ImageUploadHelpers
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Enregistre une image base64 dans /public/upload et retourne le nom du fichier
     *
     * @param string $base64Image
     * @param bool $compress
     * @return string|null
     */
    public static function saveBase64Image($base64Image, $compress = true)
    {
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
            $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif...

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                return null; // Type non supporté
            }

            $base64Image = base64_decode($base64Image);
            if ($base64Image === false) {
                return null;
            }

            // Nom unique pour l'image
            $imageName = 'img_' . date('Ymd_His') . '_' . uniqid() . '.' . $type;

            // Dossier de destination
            $uploadPath = public_path('upload');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0775, true);
            }
            
            // Chemin complet
            $filePath = $uploadPath . '/' . $imageName;

            file_put_contents($filePath, $base64Image);

            $finalPath = 'upload/' . $imageName;

            // Compression automatique si activée
            if ($compress) {
                try {
                    $compressionService = new ImageCompressionService();
                    $compressedPath = $compressionService->compressBase64Image(
                        'data:image/' . $type . ';base64,' . base64_encode($base64Image),
                        85,
                        'webp'
                    );
                    
                    if ($compressedPath) {
                        // Supprimer l'image originale et utiliser la version compressée
                        unlink($filePath);
                        $finalPath = $compressedPath;
                    }
                } catch (\Throwable $th) {
                    \Log::error('Erreur compression image: ' . $th->getMessage());
                    // Continuer avec l'image originale en cas d'erreur
                }
            }

            return $finalPath; // retour du chemin relatif
        }

        return null;
    }

    /**
     * Optimise une image existante
     */
    public static function optimizeImage($imagePath)
    {
        try {
            $compressionService = new ImageCompressionService();
            return $compressionService->compressImage($imagePath);
        } catch (\Throwable $th) {
            \Log::error('Erreur optimisation image: ' . $th->getMessage());
            return $imagePath;
        }
    }

    /**
     * Génère des thumbnails pour une image
     */
    public static function generateThumbnails($imagePath, $sizes = [150, 300, 600])
    {
        try {
            $compressionService = new ImageCompressionService();
            return $compressionService->generateThumbnails($imagePath, $sizes);
        } catch (\Throwable $th) {
            \Log::error('Erreur génération thumbnails: ' . $th->getMessage());
            return [];
        }
    }
}
