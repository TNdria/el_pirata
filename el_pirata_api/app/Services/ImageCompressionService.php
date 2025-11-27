<?php

namespace App\Services;

use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ImageCompressionService
{
    /**
     * Compresse et optimise une image
     */
    public function compressImage($imagePath, $quality = 85, $format = 'webp')
    {
        try {
            $fullPath = storage_path('app/public/' . $imagePath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception('Image file not found');
            }

            // Créer l'image avec Intervention Image
            $image = Image::make($fullPath);
            
            // Optimiser selon le format
            switch ($format) {
                case 'webp':
                    $image->encode('webp', $quality);
                    break;
                case 'avif':
                    $image->encode('avif', $quality);
                    break;
                case 'jpg':
                case 'jpeg':
                    $image->encode('jpg', $quality);
                    break;
                case 'png':
                    $image->encode('png');
                    break;
                default:
                    $image->encode('webp', $quality);
            }

            // Redimensionner si trop grande (max 1920px de largeur)
            if ($image->width() > 1920) {
                $image->resize(1920, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
            }

            // Sauvegarder l'image optimisée
            $newPath = $this->generateOptimizedPath($imagePath, $format);
            $image->save(storage_path('app/public/' . $newPath));

            return $newPath;

        } catch (\Throwable $th) {
            \Log::error('Erreur compression image: ' . $th->getMessage());
            return $imagePath; // Retourner le chemin original en cas d'erreur
        }
    }

    /**
     * Génère le chemin pour l'image optimisée
     */
    private function generateOptimizedPath($originalPath, $format)
    {
        $pathInfo = pathinfo($originalPath);
        $directory = $pathInfo['dirname'];
        $filename = $pathInfo['filename'];
        
        return $directory . '/' . $filename . '_optimized.' . $format;
    }

    /**
     * Compresse une image base64
     */
    public function compressBase64Image($base64Image, $quality = 85, $format = 'webp')
    {
        try {
            // Décoder l'image base64
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
                $type = strtolower($type[1]);

                if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                    throw new \Exception('Type d\'image non supporté');
                }

                $imageData = base64_decode($base64Image);
                if ($imageData === false) {
                    throw new \Exception('Erreur de décodage base64');
                }

                // Créer l'image avec Intervention Image
                $image = Image::make($imageData);
                
                // Redimensionner si nécessaire
                if ($image->width() > 1920) {
                    $image->resize(1920, null, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                // Optimiser selon le format
                switch ($format) {
                    case 'webp':
                        $image->encode('webp', $quality);
                        break;
                    case 'avif':
                        $image->encode('avif', $quality);
                        break;
                    case 'jpg':
                    case 'jpeg':
                        $image->encode('jpg', $quality);
                        break;
                    case 'png':
                        $image->encode('png');
                        break;
                    default:
                        $image->encode('webp', $quality);
                }

                // Générer un nom unique
                $filename = 'compressed_' . date('Ymd_His') . '_' . uniqid() . '.' . $format;
                $path = 'upload/' . $filename;

                // Sauvegarder
                $fullPath = public_path($path);
                $image->save($fullPath);

                return $path;

            } else {
                throw new \Exception('Format base64 invalide');
            }

        } catch (\Throwable $th) {
            \Log::error('Erreur compression base64: ' . $th->getMessage());
            throw $th;
        }
    }

    /**
     * Optimise automatiquement toutes les images d'un répertoire
     */
    public function optimizeDirectory($directory, $recursive = true)
    {
        $files = Storage::disk('public')->allFiles($directory);
        $optimized = 0;
        $errors = 0;

        foreach ($files as $file) {
            if ($this->isImageFile($file)) {
                try {
                    $this->compressImage($file);
                    $optimized++;
                } catch (\Throwable $th) {
                    \Log::error("Erreur optimisation {$file}: " . $th->getMessage());
                    $errors++;
                }
            }
        }

        return [
            'optimized' => $optimized,
            'errors' => $errors,
            'total' => count($files)
        ];
    }

    /**
     * Vérifie si un fichier est une image
     */
    private function isImageFile($file)
    {
        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        return in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
    }

    /**
     * Génère différentes tailles d'images (thumbnails)
     */
    public function generateThumbnails($imagePath, $sizes = [150, 300, 600])
    {
        try {
            $fullPath = storage_path('app/public/' . $imagePath);
            
            if (!file_exists($fullPath)) {
                throw new \Exception('Image file not found');
            }

            $thumbnails = [];
            $pathInfo = pathinfo($imagePath);
            $directory = $pathInfo['dirname'];
            $filename = $pathInfo['filename'];
            $extension = $pathInfo['extension'];

            foreach ($sizes as $size) {
                $image = Image::make($fullPath);
                
                // Redimensionner en gardant les proportions
                $image->resize($size, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });

                // Générer le nom du thumbnail
                $thumbnailPath = $directory . '/' . $filename . '_' . $size . '.' . $extension;
                
                // Sauvegarder
                $image->save(storage_path('app/public/' . $thumbnailPath));
                
                $thumbnails[] = $thumbnailPath;
            }

            return $thumbnails;

        } catch (\Throwable $th) {
            \Log::error('Erreur génération thumbnails: ' . $th->getMessage());
            return [];
        }
    }
}

