// =============================================================================
// blog/frontend/types/index.ts
// Définitions TypeScript globales pour la plateforme blog
// =============================================================================

// ---------------------------------------------------------------------------
// Entités métier
// ---------------------------------------------------------------------------

/** Rôles disponibles pour un utilisateur */
export type UserRole = "admin" | "author" | "subscriber";

/** Statut de publication d'un article */
export type ArticleStatus = "draft" | "published" | "archived";

/** Types de toast pour les notifications UI */
export type ToastType = "success" | "error" | "warning" | "info";

/** Types MIME autorisés pour les médias */
export type MimeType =
  | "image/jpeg"
  | "image/png"
  | "image/webp"
  | "image/gif"
  | "image/svg+xml"
  | "application/pdf"
  | string;

// ---------------------------------------------------------------------------
// Modèles principaux
// ---------------------------------------------------------------------------

/** Représente un utilisateur de la plateforme */
export interface User {
  id: number;
  name: string;
  email: string;
  role: UserRole;
  avatar: string | null;
  created_at: string;
}

/** Représente un fichier média attaché à un article */
export interface Media {
  id: number;
  filename: string;
  path: string;
  url: string;
  mime_type: MimeType;
  size: number;
  created_at: string;
}

/** Représente un article de blog */
export interface Article {
  id: number;
  title: string;
  slug: string;
  excerpt: string | null;
  content: string;
  cover_image: string | null;
  status: ArticleStatus;
  views_count: number;
  reading_time: number;
  published_at: string | null;
  created_at: string;
  updated_at: string;
  author: User;
  comments_count: number;
  media?: Media[];
}

/** Représente un commentaire sur un article */
export interface Comment {
  id: number;
  content: string;
  is_approved: boolean;
  created_at: string;
  author: User;
}

/** Représente un abonné à la newsletter */
export interface Subscriber {
  id: number;
  email: string;
  subscribed_at: string;
  unsubscribed_at: string | null;
  is_active: boolean;
}

// ---------------------------------------------------------------------------
// Réponses API génériques
// ---------------------------------------------------------------------------

/** Métadonnées de pagination renvoyées par l'API */
export interface PaginationMeta {
  current_page: number;
  last_page: number;
  per_page: number;
  total: number;
  from: number | null;
  to: number | null;
}

/** Réponse API générique encapsulant une donnée T */
export interface ApiResponse<T> {
  success: boolean;
  data: T;
  message: string;
  meta?: PaginationMeta;
}

/** Réponse API paginée : data est un tableau de T */
export interface PaginatedResponse<T> extends ApiResponse<T[]> {
  meta: PaginationMeta;
}

// ---------------------------------------------------------------------------
// États Pinia
// ---------------------------------------------------------------------------

/** État du store d'authentification */
export interface AuthState {
  user: User | null;
  token: string | null;
  loading: boolean;
  error: string | null;
}

/** État du store des articles */
export interface ArticlesState {
  articles: Article[];
  currentArticle: Article | null;
  loading: boolean;
  error: string | null;
  pagination: PaginationMeta | null;
  filters: ArticleFilters;
}

/** État du store UI */
export interface UiState {
  toasts: Toast[];
  isLoading: boolean;
  sidebarOpen: boolean;
}

// ---------------------------------------------------------------------------
// Filtres et formulaires
// ---------------------------------------------------------------------------

/** Filtres de recherche pour la liste des articles */
export interface ArticleFilters {
  search?: string;
  status?: ArticleStatus | "";
  category?: string;
  per_page?: number;
  page?: number;
  author_id?: number;
  sort_by?: "created_at" | "published_at" | "views_count" | "title";
  sort_direction?: "asc" | "desc";
}

/** Payload pour créer ou mettre à jour un article */
export interface ArticlePayload {
  title: string;
  content: string;
  excerpt?: string;
  cover_image?: string | null;
  status?: ArticleStatus;
  media_ids?: number[];
}

/** Payload de connexion */
export interface LoginCredentials {
  email: string;
  password: string;
}

/** Payload d'inscription */
export interface RegisterPayload {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

/** Payload pour s'abonner / se désabonner à la newsletter */
export interface NewsletterPayload {
  email: string;
}

/** Filtres pour la liste des abonnés (admin) */
export interface SubscriberFilters {
  is_active?: boolean;
  per_page?: number;
  page?: number;
  search?: string;
}

// ---------------------------------------------------------------------------
// UI
// ---------------------------------------------------------------------------

/** Notification toast affichée dans l'interface */
export interface Toast {
  id: string;
  message: string;
  type: ToastType;
  /** Durée d'affichage en millisecondes (0 = persistant) */
  duration: number;
}

/** Options pour créer un toast */
export interface ToastOptions {
  message: string;
  type?: ToastType;
  duration?: number;
}

// ---------------------------------------------------------------------------
// Helpers / utilitaires
// ---------------------------------------------------------------------------

/** Rend toutes les propriétés de T optionnelles sauf celles listées dans K */
export type PartialExcept<T, K extends keyof T> = Partial<T> & Pick<T, K>;

/** Extrait le type de la propriété `data` d'une ApiResponse */
export type ApiData<T extends ApiResponse<unknown>> = T["data"];

/** Représente une erreur HTTP structurée */
export interface HttpError {
  statusCode: number;
  statusMessage: string;
  message?: string;
  data?: Record<string, string[]>; // erreurs de validation Laravel
}
