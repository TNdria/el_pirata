import {
  required,
  minLength,
  maxLength,
  email,
  numeric,
} from '@vuelidate/validators'
import { helpers } from '@vuelidate/validators'

// Champ requis
export const requiredField = helpers.withMessage(
  'Ce champ est requis',
  required
)

// Longueur minimale
export const minLen = (length: number) =>
  helpers.withMessage(
    `Le champ doit contenir au moins ${length} caractères`,
    minLength(length)
  )

// Longueur maximale
export const maxLen = (length: number) =>
  helpers.withMessage(
    `Le champ ne peut pas dépasser ${length} caractères`,
    maxLength(length)
  )

// Email valide
export const validEmail = helpers.withMessage(
  "L'email n'est pas valide",
  email
)

// Numérique uniquement
export const isNumeric = helpers.withMessage(
  'Le champ doit être un nombre',
  numeric
)

// Comparaison de mot de passe
export const sameAsPassword = (getPassword: () => string) =>
  helpers.withMessage(
    'Les mots de passe ne correspondent pas.',
    (value: string) => value === getPassword()
  )

export const requiredIf = (condition: () => boolean) =>
  helpers.withMessage(
    'Ce champ est requis',
    (value: any) => condition() ? !!value : true
  )