<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use App\Models\Article;
use App\Models\Comment;
use App\Models\Subscriber;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // ---------------------------------------------------------------------
        // 1. UTILISATEURS
        // ---------------------------------------------------------------------
        $admin = User::create([
            'name'              => 'Vladmir K.',
            'email'             => 'admin@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'admin',
            'avatar'            => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);

        $author = User::create([
            'name'              => 'Alice Martin',
            'email'             => 'author@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'author',
            'avatar'            => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);

        $reader1 = User::create([
            'name'              => 'Jean Dupont',
            'email'             => 'reader@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'reader',
            'avatar'            => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);

        $reader2 = User::create([
            'name'              => 'Sophie Laurent',
            'email'             => 'sophie@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'reader',
            'avatar'            => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=150&auto=format&fit=crop&q=80',
            'email_verified_at' => now(),
        ]);

        // ---------------------------------------------------------------------
        // 2. ARTICLES (Riches & Professionnels)
        // ---------------------------------------------------------------------
        $articlesData = [
            [
                'user_id'      => $admin->id,
                'title'        => 'L\'essor de l\'Intelligence Artificielle en 2026 : Au-delà des Chatbots',
                'excerpt'      => 'Découvrez comment les modèles d\'IA autonomes et les architectures d\'agents complexes transforment nos flux de travail et nos industries cette année.',
                'content'      => "L'année 2026 marque un tournant décisif dans l'évolution de l'intelligence artificielle. Nous sommes passés de l'ère des simples chatbots conversationnels à celle des **agents autonomes multicouches**, capables de planifier, d'exécuter et d'optimiser des tâches complexes avec une supervision humaine minimale.\n\n### Des modèles de langage aux systèmes multi-agents\n\nLes architectures modernes n'utilisent plus un seul grand modèle linguistique (LLM) de manière isolée. Elles orchestrent désormais des réseaux d'agents spécialisés : Un agent de recherche extrait les données fraîches, un agent de rédaction structure le contenu, et un agent de critique vérifie la conformité et la véracité des faits.\n\nCe paradigme collaboratif résout une grande partie des problèmes d'hallucinations et de manque de précision logique. Pour les développeurs et les entreprises, cela signifie la possibilité de déléguer des tâches entières de support, d'analyse de données et même de développement logiciel.\n\n### L'impact sur le quotidien des créateurs\n\nEn tant que créateurs, l'IA ne nous remplace pas, elle nous dote de capacités démultipliées. Imaginez pouvoir traduire, adapter, illustrer et distribuer votre pensée dans dix langues simultanément tout en conservant votre identité stylistique unique. C'est la promesse de cette nouvelle vague technologique.",
                'cover_image'  => 'https://images.unsplash.com/photo-1620712943543-bcc4688e7485?w=1200&q=80',
                'status'       => 'published',
                'views_count'  => 542,
                'published_at' => now()->subDays(2),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'L\'art du Minimalisme dans le Design d\'Interface Moderne',
                'excerpt'      => 'Pourquoi moins c\'est plus. Analyse des principes de design épurés qui améliorent considérablement l\'expérience utilisateur et la vitesse de chargement.',
                'content'      => "Le design minimaliste n'est pas simplement l'absence de décoration ; c'est la recherche de la clarté absolue. Dans un web saturé d'informations, d'animations bruyantes et de popups agressifs, les interfaces minimalistes offrent un havre de paix visuel hautement fonctionnel.\n\n### 1. La hiérarchie visuelle par le vide\n\nL'espace blanc (ou espace négatif) n'est pas du vide perdu, c'est un élément actif de la conception. Il permet de guider le regard de l'utilisateur directement vers les actions prioritaires, comme les boutons d'appel à l'action ou les titres majeurs. Plus un élément est entouré de vide, plus il gagne en importance.\n\n### 2. Une palette chromatique restreinte et contrastée\n\nLimiter les teintes permet d'éviter la surcharge cognitive. Choisissez une couleur dominante neutre (souvent sombre ou très claire), une couleur secondaire douce, et une unique couleur d'accentuation vibrante pour signaler l'interactivité.\n\n### 3. La performance au service du design\n\nUn design minimaliste pèse moins lourd. Moins d'images superflues, moins de scripts complexes d'animation conduisent à un temps de chargement ultra-rapide. L'esthétique s'aligne ainsi parfaitement avec les exigences techniques modernes de vitesse et de SEO.",
                'cover_image'  => 'https://images.unsplash.com/photo-1507238691740-187a5b1d37b8?w=1200&q=80',
                'status'       => 'published',
                'views_count'  => 389,
                'published_at' => now()->subDays(5),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'Pourquoi Nuxt 3 est le Framework Nuageux Ultime en 2026',
                'excerpt'      => 'De l\'auto-import révolutionnaire à la puissance du rendu hybride sur le Edge, explorez pourquoi Nuxt 3 s\'impose définitivement pour la performance web.',
                'content'      => "Dans l'écosystème de développement web en constante évolution, **Nuxt 3** s'est affirmé comme la référence absolue pour concevoir des applications web basées sur Vue.js.\n\n### L'auto-import intelligent : Un confort inégalé\n\nL'une des plus belles réussites de Nuxt 3 réside dans son système d'auto-importation. Plus besoin d'écrire des dizaines de lignes d'imports fastidieux en haut de chaque composant pour les `ref`, `computed`, ou vos propres composables personnalisés. Tout fonctionne de manière transparente sous le capot, augmentant considérablement la vitesse de développement et la clarté du code.\n\n### Le Rendu Hybride et le Edge Rendering\n\nAvec le moteur Nitro intégré, Nuxt 3 vous permet de choisir la stratégie de rendu parfaite pour chaque route de votre application :\n- **SSR (Server-Side Rendering)** pour les pages dynamiques nécessitant un bon SEO.\n- **Static (SSG)** pour vos articles de blog ou pages marketing performantes.\n- **SPA** pour les tableaux de bord interactifs réservés aux membres.\n\nEn déployant Nuxt 3 sur des réseaux Edge comme Vercel ou Netlify, vos requêtes serveur s'exécutent au plus près physiquement de vos utilisateurs, garantissant des temps de réponse sous la barre des 50 millisecondes.",
                'cover_image'  => 'https://images.unsplash.com/photo-1555066931-4365d14bab8c?w=1200&q=80',
                'status'       => 'published',
                'views_count'  => 712,
                'published_at' => now()->subDays(8),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'Construire un SaaS à l\'ère du Solopreneuriat en 2026',
                'excerpt'      => 'Comment un développeur individuel peut aujourd\'hui concevoir, déployer et monétiser un produit scalable en un temps record grâce aux outils modernes.',
                'content'      => "Il n'a jamais été aussi propice de se lancer en tant que solopreneur. L'avènement des outils low-code, des APIs managées et des plateformes d'hébergement modernes permet à une seule personne de gérer un produit SaaS générant des milliers d'euros de revenus récurrents.\n\n### La focalisation absolue sur la valeur ajoutée\n\nLa clé du solopreneur moderne est de ne pas réinventer la roue. N'écrivez pas votre propre système de facturation : utilisez **Stripe**. N'hébergez pas vos propres serveurs de base de données complexes : utilisez **Supabase** ou **PlanetScale**. Déléguez tout ce qui ne constitue pas le cœur de votre produit pour vous concentrer uniquement sur la résolution du problème de votre client.\n\n### Le marketing par le contenu et le 'Build in Public'\n\nConstruire en public consiste à partager vos victoires, vos échecs, vos chiffres et votre progression sur les réseaux sociaux. Cela crée une communauté engagée autour de vous avant même le lancement officiel de votre produit. Les premiers utilisateurs de votre SaaS ne viendront pas de publicités payantes coûteuses, mais de la confiance que vous aurez instaurée au fil de votre parcours.",
                'cover_image'  => 'https://images.unsplash.com/photo-1460925895917-afdab827c52f?w=1200&q=80',
                'status'       => 'published',
                'views_count'  => 291,
                'published_at' => now()->subDays(12),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'Les Secrets d\'une Productivité Maximale sans Burnout',
                'excerpt'      => 'Méthodes concrètes et gestion du temps basées sur les neurosciences pour optimiser vos journées de travail tout en préservant activement votre santé mentale.',
                'content'      => "La productivité ne consiste pas à travailler plus d'heures ; elle consiste à rendre chaque heure de travail intensément focalisée et efficace, tout en s'octroyant le repos mérité pour durer sur le long terme.\n\n### Le travail ultra-profond (Deep Work)\n\nNotre cerveau n'est pas conçu pour le multitâche. Chaque distraction (une notification de messagerie, un onglet ouvert) consomme ce que les psychologues appellent le *résidu d'attention*. Pour entrer dans un état de flux créatif optimal, réservez des blocs de 90 minutes totalement isolés de toute perturbation externe.\n\n### La règle des 80/20 appliquée au quotidien\n\nIdentifiez les 20 % de vos tâches quotidiennes qui génèrent 80 % de vos résultats réels. Concentrez vos premières heures de la journée — lorsque votre énergie mentale est à son apogée — sur ces tâches clés. Le reste peut être traité plus tard dans la journée, délégué, ou tout simplement éliminé.",
                'cover_image'  => 'https://images.unsplash.com/photo-1484480974693-6ca0a78fb36b?w=1200&q=80',
                'status'       => 'published',
                'views_count'  => 415,
                'published_at' => now()->subDays(15),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'Article Futuriste en cours de Rédaction',
                'excerpt'      => 'Ceci est un article de démonstration conservé au statut de brouillon pour illustrer les flux de publication dans l\'interface administrative.',
                'content'      => "Le contenu de cet article est actuellement un brouillon secret réservé aux yeux des administrateurs et des éditeurs du blog moderne. Une fois validé, il sera publié en un clic !",
                'cover_image'  => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?w=1200&q=80',
                'status'       => 'draft',
                'views_count'  => 0,
                'published_at' => null,
            ],
        ];

        foreach ($articlesData as $data) {
            Article::create($data);
        }

        // ---------------------------------------------------------------------
        // 3. COMMENTAIRES (Interactifs et Réels)
        // ---------------------------------------------------------------------
        $articles = Article::all();

        foreach ($articles as $article) {
            if ($article->status === 'published') {
                Comment::create([
                    'article_id'  => $article->id,
                    'user_id'     => $reader1->id,
                    'content'     => 'Wow, excellent article ! Les explications sont d\'une clarté remarquable, merci pour ce partage de qualité.',
                    'is_approved' => true,
                ]);

                Comment::create([
                    'article_id'  => $article->id,
                    'user_id'     => $reader2->id,
                    'content'     => 'Je suis tout à fait d\'accord avec votre point de vue. C\'est un sujet passionnant à suivre de près.',
                    'is_approved' => true,
                ]);

                Comment::create([
                    'article_id'  => $article->id,
                    'user_id'     => $author->id,
                    'content'     => 'Merci pour vos retours constructifs ! N\'hésitez pas à partager l\'article autour de vous.',
                    'is_approved' => true,
                ]);
            }
        }

        // ---------------------------------------------------------------------
        // 4. ABONNÉS NEWSLETTER
        // ---------------------------------------------------------------------
        Subscriber::create([
            'email'         => 'jean.dupont@example.com',
            'token'         => Str::uuid()->toString(),
            'subscribed_at' => now()->subMonths(1),
        ]);

        Subscriber::create([
            'email'         => 'sophie.laurent@example.com',
            'token'         => Str::uuid()->toString(),
            'subscribed_at' => now()->subWeeks(2),
        ]);
    }
}
