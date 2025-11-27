<?php

namespace App\Services;

class IntelligentValidationService
{
    /**
     * Valide une réponse d'énigme avec correspondance floue
     */
    public function validateAnswer(string $userAnswer, string $correctAnswer, array $options = []): array
    {
        // Normalisation des réponses
        $userAnswer = $this->normalizeText($userAnswer);
        $correctAnswer = $this->normalizeText($correctAnswer);

        // Correspondance exacte
        if ($userAnswer === $correctAnswer) {
            return [
                'is_correct' => true,
                'confidence' => 100,
                'method' => 'exact_match'
            ];
        }

        // Correspondance de mots-clés
        $keywordMatch = $this->checkKeywordMatch($userAnswer, $correctAnswer);
        if ($keywordMatch['is_correct']) {
            return $keywordMatch;
        }

        // Distance de Levenshtein
        $levenshteinMatch = $this->checkLevenshteinDistance($userAnswer, $correctAnswer);
        if ($levenshteinMatch['is_correct']) {
            return $levenshteinMatch;
        }

        // Correspondance de synonymes
        $synonymMatch = $this->checkSynonymMatch($userAnswer, $correctAnswer);
        if ($synonymMatch['is_correct']) {
            return $synonymMatch;
        }

        // Correspondance partielle
        $partialMatch = $this->checkPartialMatch($userAnswer, $correctAnswer);
        if ($partialMatch['is_correct']) {
            return $partialMatch;
        }

        return [
            'is_correct' => false,
            'confidence' => 0,
            'method' => 'no_match',
            'suggestions' => $this->generateSuggestions($userAnswer, $correctAnswer)
        ];
    }

    /**
     * Normalise le texte pour la comparaison
     */
    private function normalizeText(string $text): string
    {
        // Convertir en minuscules
        $text = mb_strtolower($text, 'UTF-8');
        
        // Supprimer les accents
        $text = $this->removeAccents($text);
        
        // Supprimer les espaces multiples et les caractères spéciaux
        $text = preg_replace('/[^\w\s]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        
        // Supprimer les espaces en début et fin
        return trim($text);
    }

    /**
     * Supprime les accents
     */
    private function removeAccents(string $text): string
    {
        $accents = [
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e',
            'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o',
            'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u',
            'ý' => 'y', 'ÿ' => 'y',
            'ç' => 'c', 'ñ' => 'n'
        ];

        return strtr($text, $accents);
    }

    /**
     * Vérifie la correspondance de mots-clés
     */
    private function checkKeywordMatch(string $userAnswer, string $correctAnswer): array
    {
        $userWords = explode(' ', $userAnswer);
        $correctWords = explode(' ', $correctAnswer);
        
        $matches = 0;
        $totalWords = count($correctWords);
        
        foreach ($correctWords as $correctWord) {
            if (strlen($correctWord) < 3) continue; // Ignorer les mots trop courts
            
            foreach ($userWords as $userWord) {
                if (strlen($userWord) < 3) continue;
                
                // Correspondance exacte du mot
                if ($correctWord === $userWord) {
                    $matches++;
                    break;
                }
                
                // Correspondance partielle (le mot correct est contenu dans la réponse utilisateur)
                if (strpos($userWord, $correctWord) !== false || strpos($correctWord, $userWord) !== false) {
                    $matches++;
                    break;
                }
            }
        }
        
        $confidence = ($matches / $totalWords) * 100;
        
        return [
            'is_correct' => $confidence >= 70, // 70% des mots-clés doivent correspondre
            'confidence' => $confidence,
            'method' => 'keyword_match'
        ];
    }

    /**
     * Vérifie la distance de Levenshtein
     */
    private function checkLevenshteinDistance(string $userAnswer, string $correctAnswer): array
    {
        $distance = levenshtein($userAnswer, $correctAnswer);
        $maxLength = max(strlen($userAnswer), strlen($correctAnswer));
        
        if ($maxLength === 0) {
            return [
                'is_correct' => false,
                'confidence' => 0,
                'method' => 'levenshtein'
            ];
        }
        
        $similarity = (1 - ($distance / $maxLength)) * 100;
        
        return [
            'is_correct' => $similarity >= 85, // 85% de similarité minimum
            'confidence' => $similarity,
            'method' => 'levenshtein'
        ];
    }

    /**
     * Vérifie la correspondance de synonymes
     */
    private function checkSynonymMatch(string $userAnswer, string $correctAnswer): array
    {
        $synonyms = [
            'pirate' => ['corsaire', 'flibustier', 'bucanier', 'forban'],
            'trésor' => ['butin', 'richesse', 'or', 'argent', 'bijoux'],
            'bateau' => ['navire', 'vaisseau', 'embarcation'],
            'mer' => ['océan', 'eau', 'flots'],
            'île' => ['atoll', 'îlot'],
            'carte' => ['plan', 'schéma'],
            'compas' => ['boussole'],
            'coffre' => ['caisse', 'boîte'],
            'clé' => ['clef'],
            'porte' => ['entrée', 'ouverture'],
            'chemin' => ['route', 'sentier', 'piste'],
            'montagne' => ['colline', 'sommet', 'pic'],
            'forêt' => ['bois', 'jungle'],
            'rivière' => ['fleuve', 'cours d\'eau'],
            'pont' => ['passerelle'],
            'château' => ['forteresse', 'citadelle'],
            'tunnel' => ['souterrain', 'galerie'],
            'grotte' => ['caverne', 'antre'],
            'phare' => ['tour', 'balise'],
            'port' => ['harbour', 'quai'],
        ];

        $userWords = explode(' ', $userAnswer);
        $correctWords = explode(' ', $correctAnswer);
        
        $matches = 0;
        $totalWords = count($correctWords);
        
        foreach ($correctWords as $correctWord) {
            if (strlen($correctWord) < 3) continue;
            
            $found = false;
            
            // Vérifier correspondance directe
            foreach ($userWords as $userWord) {
                if ($correctWord === $userWord) {
                    $matches++;
                    $found = true;
                    break;
                }
            }
            
            // Vérifier synonymes
            if (!$found) {
                foreach ($synonyms as $key => $synonymList) {
                    if (in_array($correctWord, $synonymList) || $correctWord === $key) {
                        foreach ($userWords as $userWord) {
                            if (in_array($userWord, $synonymList) || $userWord === $key) {
                                $matches++;
                                $found = true;
                                break 2;
                            }
                        }
                    }
                }
            }
        }
        
        $confidence = ($matches / $totalWords) * 100;
        
        return [
            'is_correct' => $confidence >= 75,
            'confidence' => $confidence,
            'method' => 'synonym_match'
        ];
    }

    /**
     * Vérifie la correspondance partielle
     */
    private function checkPartialMatch(string $userAnswer, string $correctAnswer): array
    {
        // Vérifier si la réponse correcte est contenue dans la réponse utilisateur
        if (strpos($userAnswer, $correctAnswer) !== false) {
            return [
                'is_correct' => true,
                'confidence' => 90,
                'method' => 'partial_containment'
            ];
        }
        
        // Vérifier si la réponse utilisateur est contenue dans la réponse correcte
        if (strpos($correctAnswer, $userAnswer) !== false && strlen($userAnswer) >= 3) {
            return [
                'is_correct' => true,
                'confidence' => 80,
                'method' => 'partial_containment'
            ];
        }
        
        return [
            'is_correct' => false,
            'confidence' => 0,
            'method' => 'partial_match'
        ];
    }

    /**
     * Génère des suggestions basées sur la réponse incorrecte
     */
    private function generateSuggestions(string $userAnswer, string $correctAnswer): array
    {
        $suggestions = [];
        
        // Suggestion basée sur la longueur
        if (strlen($userAnswer) < strlen($correctAnswer) * 0.5) {
            $suggestions[] = "Votre réponse semble trop courte. Essayez d'être plus précis.";
        } elseif (strlen($userAnswer) > strlen($correctAnswer) * 2) {
            $suggestions[] = "Votre réponse semble trop longue. Essayez d'être plus concis.";
        }
        
        // Suggestion basée sur les mots-clés
        $correctWords = explode(' ', $correctAnswer);
        $userWords = explode(' ', $userAnswer);
        
        $missingWords = array_diff($correctWords, $userWords);
        if (!empty($missingWords)) {
            $suggestions[] = "Il manque peut-être le(s) mot(s) : " . implode(', ', array_slice($missingWords, 0, 3));
        }
        
        return $suggestions;
    }

    /**
     * Valide une réponse avec validation manuelle si nécessaire
     */
    public function validateWithManualCheck(string $userAnswer, string $correctAnswer, bool $allowManualValidation = true): array
    {
        $result = $this->validateAnswer($userAnswer, $correctAnswer);
        
        // Si la validation automatique échoue mais que la confiance est élevée, proposer validation manuelle
        if (!$result['is_correct'] && $result['confidence'] >= 60 && $allowManualValidation) {
            $result['requires_manual_validation'] = true;
            $result['manual_validation_reason'] = 'Confiance élevée mais validation automatique échouée';
        }
        
        return $result;
    }
}

