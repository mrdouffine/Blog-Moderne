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
            'name'              => 'DOUFFAN Kouassi vladimir Pierrick ELom',
            'email'             => 'kouassidouffan@gmail.com',
            'password'          => Hash::make('vladimir123'),
            'role'              => 'admin',
            'avatar'            => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150&auto=format&fit=crop&q=80', 'avatars', 'admin.jpg'),
            'email_verified_at' => now(),
        ]);

        $author = User::create([
            'name'              => 'MENSAH audrey',
            'email'             => 'audrey@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'author',
            'avatar'            => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150&auto=format&fit=crop&q=80', 'avatars', 'author.jpg'),
            'email_verified_at' => now(),
        ]);

        $author2 = User::create([
            'name'              => 'SAHM maimouna',
            'email'             => 'maimouna@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'author',
            'avatar'            => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150&auto=format&fit=crop&q=80', 'avatars', 'author2.jpg'),
            'email_verified_at' => now(),
        ]);

        $reader1 = User::create([
            'name'              => 'ADENYO pascaline',
            'email'             => 'pascaline@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'reader',
            'avatar'            => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1539571696357-5a69c17a67c6?w=150&auto=format&fit=crop&q=80', 'avatars', 'reader1.jpg'),
            'email_verified_at' => now(),
        ]);

        $reader2 = User::create([
            'name'              => 'KEKEY prisca',
            'email'             => 'prisca@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'reader',
            'avatar'            => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?w=150&auto=format&fit=crop&q=80', 'avatars', 'reader2.jpg'),
            'email_verified_at' => now(),
        ]);

        $reader3 = User::create([
            'name'              => 'KOULEFIANOU therese',
            'email'             => 'therese@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'reader',
            'avatar'            => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1544005313-94ddf0286df2?w=150&auto=format&fit=crop&q=80', 'avatars', 'reader3.jpg'),
            'email_verified_at' => now(),
        ]);

        $reader4 = User::create([
            'name'              => 'TCHADJOBO MANCHOUR',
            'email'             => 'manchour@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'reader',
            'avatar'            => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?w=150&auto=format&fit=crop&q=80', 'avatars', 'reader4.jpg'),
            'email_verified_at' => now(),
        ]);

        $reader5 = User::create([
            'name'              => 'DOUFFAN prevael',
            'email'             => 'prevael@blog.local',
            'password'          => Hash::make('password'),
            'role'              => 'reader',
            'avatar'            => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150&auto=format&fit=crop&q=80', 'avatars', 'reader5.jpg'),
            'email_verified_at' => now(),
        ]);

        // ---------------------------------------------------------------------
        // 2. ARTICLES (Riches & Professionnels)
        // ---------------------------------------------------------------------
        $articlesData = [
            [
                'user_id'      => $admin->id,
                'title'        => 'Les Reines du Textile : L’Histoire Fascinante des Nana Benz du Togo',
                'excerpt'      => 'Découvrez comment les célèbres femmes d’affaires de Lomé ont dominé le commerce du wax et façonné l’économie et l’indépendance du Togo.',
                'content'      => "Les **Nana Benz** ne sont pas seulement des commerçantes ; elles sont des figures légendaires de l'histoire économique et sociale du Togo. Au milieu du XXe siècle, ces femmes audacieuses ont pris les rênes du commerce exclusif du tissu Wax hollandais en Afrique de l'Ouest, bâtissant de véritables empires financiers.\n\n### Les Origines d’un Pouvoir Économique Unique\n\nParties de rien dans le grand marché de Lomé (**Assigamé**), ces femmes ont su négocier des contrats de distribution exclusive avec les fabricants européens. Grâce à leur sens inné des affaires et à leur profonde compréhension des goûts de leur clientèle, elles ont créé des motifs aux noms évocateurs devenus de véritables symboles culturels (comme *« L’œil de ma rivale »* ou *« Si tu sors, je sors »*).\n\n### Les Mercedes-Benz : Symbole de Réussite\n\nLeur surnom « Nana Benz » vient du fait qu'elles étaient les seules dans les années 70 et 80 à posséder des flottes de voitures de luxe allemandes Mercedes-Benz, qu'elles louaient parfois à l'État pour transporter les dignitaires étrangers en visite officielle.\n\nAu-delà de leur richesse personnelle, elles ont financé des écoles, soutenu activement la lutte pour l'indépendance nationale et formé des générations de jeunes entrepreneuses. Elles restent aujourd'hui le plus bel exemple d'indépendance financière et d'empowerment des femmes au Togo.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1590736969955-71cc94801759?w=1200&q=80', 'covers', 'nana_benz.jpg'),
                'category'     => 'Histoire & Société',
                'status'       => 'published',
                'views_count'  => 854,
                'published_at' => now()->subDays(1),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'Joyaux Gastronomiques du Togo : Fufu, Ablo, Pinon et Sauces Traditionnelles',
                'excerpt'      => 'Plongez au cœur des saveurs togolaises. Un voyage culinaire à la découverte des secrets de préparation du fufu de pilon et de l’ablo fermenté.',
                'content'      => "La gastronomie togolaise est riche, variée et profondément ancrée dans les traditions locales. Elle se caractérise par l'utilisation intelligente des céréales (maïs, mil), des tubercules (manioc, igname) et d'épices parfumées.\n\n### Le Fufu : Le Rituel du Pilon\n\nLe **Fufu** est le roi de la table togolaise. Il est préparé à base d'ignames blanches bouillies, puis vigoureusement pilées dans un mortier en bois à l'aide de lourds pilons. Ce travail d'équipe exige une coordination parfaite entre le « pileur » et celui qui retourne la pâte chaude. Le fufu se déguste chaud, accompagné de sauces riches comme la sauce arachide, la sauce graine ou la sauce de poisson frais.\n\n### L'Ablo : La Douceur Fermentée\n\nL'**Ablo** est une galette de maïs légèrement sucrée, cuite à la vapeur. Le secret de sa texture aérée réside dans la fermentation naturelle de la pâte pendant plusieurs heures. Servi chaud dans des feuilles de bananier, l'Ablo accompagne parfaitement le poulet braisé ou le poisson frit, rehaussé d'un piment vert écrasé très parfumé.\n\n### Le Pinon : Le Réconfort Rapide\n\nLe **Pinon** est préparé en mélangeant du Gari (farine de manioc torréfiée) avec de l'eau bouillante et une base de sauce tomate parfumée. C'est le plat de réconfort par excellence, rapide à cuisiner mais d'une richesse incroyable en bouche.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1604329760661-e71dc83f8f26?w=1200&q=80', 'covers', 'gastronomy.jpg'),
                'category'     => 'Gastronomie',
                'status'       => 'published',
                'views_count'  => 642,
                'published_at' => now()->subDays(3),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'Le Togo Vert : 5 Merveilles Naturelles à Explorer à Kpalimé',
                'excerpt'      => 'Montagnes brumeuses, cascades cristallines et forêts tropicales luxuriantes : découvrez pourquoi Kpalimé est le paradis écologique du Togo.',
                'content'      => "Située à environ 120 kilomètres au nord-ouest de Lomé, la région de **Kpalimé** offre un contraste saisissant avec la côte. C'est une zone de montagnes, de forêts denses et de plantations de café et de cacao, prisée pour son climat frais et sa nature exubérante.\n\n### 1. Le Mont Agou : Le Toit du Togo\n\nCulminant à 986 mètres d'altitude, le **Mont Agou** est le point le plus élevé du pays. L'ascension à travers les petits villages pittoresques accrochés aux flancs de la montagne offre des panoramas grandioses sur les plaines environnantes.\n\n### 2. Les Cascades Cachées de Kpimé et Aklowa\n\nLa région regorge de chutes d'eau spectaculaires. La **cascade de Kpimé**, facilement accessible, se déverse le long d'une paroi rocheuse impressionnante. Pour les plus aventureux, la **cascade d'Aklowa** se mérite après une randonnée à travers la forêt tropicale, offrant une douche naturelle rafraîchissante en pleine jungle.\n\n### 3. Les Papillons et la Biodiversité\n\nKpalimé est mondialement réputée pour sa faune entomologique. Accompagné d'un guide local, vous découvrirez des centaines d'espèces de papillons multicolores aux motifs incroyables, vivant en harmonie au cœur de la forêt protégée.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=80', 'covers', 'kpalime.jpg'),
                'category'     => 'Voyage & Nature',
                'status'       => 'published',
                'views_count'  => 519,
                'published_at' => now()->subDays(6),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'Véganisme au Togo : Revisiter nos Recettes Traditionnelles 100% Végétales',
                'excerpt'      => 'Comment manger végétalien et local en Afrique de l’Ouest ? Une exploration des ingrédients naturellement vegans et des versions végétales de nos classiques.',
                'content'      => "Le véganisme est souvent perçu comme une tendance occidentale moderne. Pourtant, en y regardant de plus près, la cuisine traditionnelle togolaise et ouest-africaine repose historiquement sur une base végétale extrêmement riche et diversifiée.\n\n### Les Plats Naturellement Vegans du Quotidien\n\nBeaucoup de nos spécialités locales n'ont pas besoin d'être adaptées car elles sont déjà 100% végétaliennes :\n- **L'Ayimolou** : Un délicieux mélange de riz et de haricots rouges cuits ensemble, souvent servi avec du gari et une sauce tomate épicée.\n- **Le Dodo** : Des tranches de bananes plantains mûres, frites jusqu'à obtenir une caramélisation dorée et fondante.\n- **Le Djinkoumé** : Une pâte de maïs savoureuse cuite dans un bouillon de tomates et d'épices.\n\n### Végétaliser les Sauces Traditionnelles\n\nPour remplacer la viande ou le poisson séché traditionnellement présents dans nos sauces d'accompagnement, la nature togolaise nous offre des alternatives extraordinaires :\n- **Le Soumbala (Moutarde locale)** : Issu de la fermentation des graines de néré, il apporte une saveur umami profonde indispensable à la sauce Adémè ou Gboma.\n- **Les Champignons sauvages** : Récoltés localement en saison des pluies dans la région de Kpalimé, ils offrent une texture charnue parfaite pour remplacer la viande.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1540420773420-3366772f4999?w=1200&q=80', 'covers', 'veganism.jpg'),
                'category'     => 'Cuisine Végétale',
                'status'       => 'published',
                'views_count'  => 432,
                'published_at' => now()->subDays(9),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'L’Autonomie Financière des Femmes et l’Économie des Marchés à Lomé',
                'excerpt'      => 'Au-delà des Nana Benz, gros plan sur le rôle pilier des femmes commerçantes d’Assigamé et de Hedzranawoé dans le développement du Togo.',
                'content'      => "Au Togo, les marchés ne sont pas de simples lieux d'échange de marchandises ; ce sont les véritables moteurs financiers de la nation, et ce sont les **femmes** qui en détiennent les clés.\n\n### Le Rôle Pilier d'Assigamé et Hedzranawoé\n\nDu petit matin jusqu'au coucher du soleil, des milliers de femmes gèrent leurs étals avec une rigueur financière et une résilience exceptionnelles. Qu'il s'agisse de la vente de pagnes, d'épices, de fruits locaux ou de produits manufacturés, ces commerçantes dynamisent l'économie informelle qui représente plus de 70% de l'activité du pays.\n\n### La Solidarité Financière : Le Système des Tontines\n\nPour pallier les difficultés d'accès aux crédits bancaires traditionnels, les marchandes de Lomé ont mis en place des systèmes de solidarité financière très efficaces comme les **Tontines**. Chaque jour ou chaque semaine, un groupe de femmes cotise une somme fixe, et la totalité de la cagnotte est reversée à tour de rôle à l'une d'elles. Ce système autogéré permet de financer des investissements majeurs et d'assurer une sécurité financière collective sans aucun frais bancaire.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1533900298318-6b8da08a523e?w=1200&q=80', 'covers', 'assigame.jpg'),
                'category'     => 'Économie',
                'status'       => 'published',
                'views_count'  => 312,
                'published_at' => now()->subDays(12),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'Pourquoi le Retour aux Céréales Locales est Bon pour notre Santé',
                'excerpt'      => 'Sorgho, Mil, Fonio : découvrez les bienfaits nutritionnels exceptionnels de ces super-aliments traditionnels africains injustement oubliés.',
                'content'      => "L'Afrique de l'Ouest connaît une transition nutritionnelle rapide, marquée par une consommation accrue de blé importé et de produits ultra-transformés. Pourtant, notre sol regorge de trésors céréaliers d'une valeur nutritionnelle bien supérieure : **le sorgho, le mil et le fonio**.\n\n### Des Super-Aliments Riches en Nutriments\n\n- **Le Fonio** : Cette minuscule céréale préhistorique est naturellement sans gluten. Elle est riche en acides aminés essentiels (méthionine et cystine) absents du blé ou du maïs, ce qui en fait un allié de choix pour la croissance musculaire et la santé de la peau.\n- **Le Sorgho et le Mil** : Riches en fibres alimentaires, en fer et en antioxydants, ils aident à réguler le taux de sucre dans le sang, à prévenir les maladies cardiovasculaires et à stabiliser l'énergie tout au long de la journée.\n\n### Un Choix Écologique et Durable\n\nCes céréales traditionnelles sont extraordinairement adaptées aux climats arides. Elles nécessitent très peu d'eau pour pousser et résistent naturellement aux parasites, contrairement aux cultures importées. Valoriser ces cultures, c'est soutenir les agriculteurs locaux tout en préservant activement notre environnement.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=1200&q=80', 'covers', 'cereales.jpg'),
                'category'     => 'Nutrition',
                'status'       => 'published',
                'views_count'  => 491,
                'published_at' => now()->subDays(15),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'Comprendre le Vaginisme : Lever le Voile sur la Santé Intime Féminine',
                'excerpt'      => 'Un guide complet et bienveillant pour comprendre ce trouble intime méconnu qui touche des milliers de femmes, ses causes et les solutions pour en guérir.',
                'content'      => "Le vaginisme reste l’un des sujets les plus tabous de la santé sexuelle et intime féminine. Pourtant, il touche une proportion importante de femmes dans le monde. Il est temps de briser le silence, d'informer avec bienveillance et d'offrir des pistes concrètes de guérison.\n\n### Qu'est-ce que le vaginisme ?\n\nLe vaginisme se définit comme une contraction involontaire, réflexe et persistante des muscles du plancher pelvien (notamment le muscle pubo-coccygien) entourant l'entrée du vagin, dès qu'une tentative de pénétration (qu'il s'agisse d'un rapport sexuel, d'un tampon ou d'un examen gynécologique) a lieu.\n\nCette réaction physique échappe totalement au contrôle de la femme. Ce n'est ni un manque de désir, ni un caprice, mais une véritable réaction de défense réflexe du corps, souvent assimilée à un réflexe de clignement d'œil lorsqu'un objet s'en approche.\n\n### Les Causes Multidimensionnelles\n\nLes origines du vaginisme sont complexes et s'articulent généralement autour de facteurs physiologiques, émotionnels et culturels :\n\n- **Facteurs psychologiques et éducatifs** : Une éducation très rigide, la peur de la grossesse, ou des traumatismes passés peuvent associer l'intimité à la douleur ou à l'interdit dans l'inconscient.\n- **Facteurs physiques** : Une infection gynécologique mal soignée, des douleurs antérieures (dyspareunie) ou des cicatrices d'épisiotomie peuvent déclencher un réflexe d'auto-défense musculaire à long terme.\n\n### Les Solutions pour s'en libérer\n\nLa bonne nouvelle est que le vaginisme se soigne très bien. La guérison repose sur une approche multidisciplinaire et bienveillante :\n\n1. **La rééducation périnéale** : Accompagnée par un kinésithérapeute spécialisé, elle permet de réapprendre à relâcher consciemment les muscles pelviens.\n2. **La thérapie psycho-corporelle (sexologie)** : Elle aide à déconstruire les blocages inconscients, à réduire l'anxiété de performance et à réapprivoiser son corps à son propre rythme.\n3. **L’utilisation de dilatateurs vaginaux progressifs** : Utilisés dans l'intimité, ils aident le corps à enregistrer à nouveau des sensations neutres et indolores sans déclencher le réflexe de contraction.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1518156677180-95a2893f3e9f?w=1200&q=80', 'covers', 'vaginismus.jpg'),
                'category'     => 'Santé Intime',
                'status'       => 'published',
                'views_count'  => 567,
                'published_at' => now()->subDays(3),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'Guide complet pour cultiver ses propres herbes aromatiques à Lomé',
                'excerpt'      => 'Conseils pratiques et techniques faciles pour démarrer son mini-potager urbain en terrasse ou dans son jardin au Togo.',
                'content'      => "Avoir accès à des aromates frais et bio à la maison est un luxe à la portée de tous, même en milieu urbain à Lomé. Cultiver sa propre citronnelle, son basilic africain ou son persil est une activité relaxante et hautement gratifiante.\n\n### Quel contenant choisir ?\n\nSi vous n'avez pas de jardin en pleine terre, utilisez des pots en terre cuite locale ou des récipients de récupération percés au fond. La terre cuite permet aux racines de respirer et garde l'humidité plus longtemps sous le soleil togolais.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1466692476868-aef1dfb1e735?w=1200&q=80', 'covers', 'aromatiques.jpg'),
                'category'     => 'Jardinage',
                'status'       => 'published',
                'views_count'  => 312,
                'published_at' => now()->subDays(8),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'La Fièvre du Football au Togo : Passion, Ferveur et l’Héritage des Éperviers',
                'excerpt'      => 'Du grand stade de Kégué aux plages sablonneuses de Lomé, découvrez comment le ballon rond unit tout un peuple et fait battre le cœur de la nation.',
                'content'      => "Le football au Togo n'est pas seulement un sport ; c'est une religion civile, un langage universel et le ciment de l'unité nationale. Chaque week-end, les plages et les terrains vagues se transforment en arènes de passion.\n\n### L'Épopée de 2006 : La Coupe du Monde Historique\n\nL'histoire du football togolais restera à jamais marquée par la qualification historique des **Éperviers** à la Coupe du Monde 2006 en Allemagne. Menée par des figures emblématiques comme **Emmanuel Adebayor**, cette génération dorée a placé le Togo sur la carte mondiale du sport de haut niveau et a inspiré des millions de jeunes dans tout le pays.\n\n### Le Football des Plages : Le Vivier de Rêves\n\nChaque après-midi à Lomé, dès que la brise marine rafraîchit l'atmosphère, des centaines de jeunes se réunissent sur le sable fin pour des matchs improvisés. Pieds nus, avec des cages dessinées par des noix de coco ou des morceaux de bois, ils déploient une agilité technique extraordinaire. C'est ici que bat le véritable cœur du football togolais, fait de joie pure, de convivialité et de rêves de grandeur.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1579952363873-27f3bade9f55?w=1200&q=80', 'covers', 'football.jpg'),
                'category'     => 'Foot',
                'status'       => 'published',
                'views_count'  => 721,
                'published_at' => now()->subDays(2),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'L’Ombre du Baobab Étoilé : Un Conte de Réalisme Magique Ouest-Africain',
                'excerpt'      => 'Plongez dans un récit envoûtant où la frontière entre le rêve et la réalité s’efface sous les branches d’un arbre millénaire doté de conscience.',
                'content'      => "Dans le petit village d'Agou, situé au pied de la montagne brumeuse, se dresse un baobab pas comme les autres. Ses racines plongent profondément dans l'histoire de la terre, et ses feuilles semblent murmurer aux étoiles.\n\n### Le Secret d'Amivi\n\nAmivi, une jeune fille curieuse du village, passe ses après-midis à l'ombre de ce géant tranquille. Un soir de pleine lune, alors qu'elle dessine sur la terre ocre, elle entend une mélodie douce s'élever du tronc. Ce n'était pas le vent, mais la voix de l'arbre lui-même, lui contant les temps anciens où les humains et les esprits de la forêt marchaient main dans la main.\n\n### Un Voyage Onirique\n\nCe récit de fiction explore la relation mystique que nous entretenons avec la nature. À travers le personnage d'Amivi, nous découvrons un univers où les rêves deviennent réalité et où la sagesse de la terre se transmet à ceux qui savent prêter l'oreille au silence.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1512820790803-83ca734da794?w=1200&q=80', 'covers', 'fiction.jpg'),
                'category'     => 'Fiction',
                'status'       => 'published',
                'views_count'  => 398,
                'published_at' => now()->subDays(4),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'L’Art d’Aimer à l’Ère Moderne : Communication, Vulnérabilité et Intelligence Émotionnelle',
                'excerpt'      => 'Comment bâtir des relations amoureuses solides et durables aujourd’hui ? Une réflexion sur le pouvoir de l’écoute et de la vulnérabilité.',
                'content'      => "Aimer à l'époque contemporaine est à la fois un privilège et un défi. Entre la rapidité des connexions numériques et les exigences de la vie moderne, préserver l'intimité amoureuse demande un engagement conscient.\n\n### La Vulnérabilité : Clé de Voûte de l'Intimité\n\nLe véritable amour ne réside pas dans l'absence de conflits, mais dans la capacité à se montrer vulnérable devant l'autre. Exprimer ses peurs, ses espoirs et ses limites sans crainte du jugement est le socle sur lequel se construit la confiance mutuelle.\n\n### L'Écoute Active et la Résonance Émotionnelle\n\nPrendre le temps d'écouter, non pas pour répondre, mais pour comprendre, est l'un des plus beaux cadeaux que l'on puisse faire à son partenaire. L'intelligence émotionnelle en couple consiste à accueillir les émotions de l'autre avec bienveillance, créant un espace de sécurité et de résonance qui fortifie le lien au fil des années.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1516589178581-6cd7833ae3b2?w=1200&q=80', 'covers', 'amour.jpg'),
                'category'     => 'Amour',
                'status'       => 'published',
                'views_count'  => 812,
                'published_at' => now()->subDays(5),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'La Science du Baiser : Biologie, Attachement et Magie Chimique de l’Intimité',
                'excerpt'      => 'Plus qu’un simple geste romantique, le baiser est un puissant déclencheur d’endorphines et d’ocytocine. Analyse d’un rituel universel.',
                'content'      => "Le baiser est l'un des comportements les plus universels et les plus intimes de l'humanité. Mais au-delà de sa dimension romantique et culturelle, que se passe-t-il réellement dans notre corps lors de ce geste complice ?\n\n### La Philematologie : La Science du Baiser\n\nLa science qui étudie les baisers s'appelle la **philematologie**. Les chercheurs ont découvert qu'un baiser passionné sollicite plus de 30 muscles faciaux et déclenche une tempête de réactions biochimiques dans notre cerveau :\n\n- **Dopamine** : Elle procure une sensation d'euphorie et de plaisir immédiat, semblable à celle ressentie lors de la découverte de quelque chose de merveilleux.\n- **Ocytocine (L'hormone de l'attachement)** : Elle favorise le sentiment de sécurité, de proximité émotionnelle et d'attachement à long terme avec le partenaire.\n- **Sérotonine** : Elle régule l'humeur et crée un sentiment de bien-être général, réduisant instantanément le stress.\n\n### Un Indicateur Évolutif\n\nLe baiser sert également de « baromètre d'incompatibilité biologique ». Lors de l'échange de salive, notre corps analyse inconsciemment les informations génétiques de l'autre (notamment le complexe majeur d'histocompatibilité), nous aidant ainsi à évaluer la compatibilité biologique de notre partenaire potentiel.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1518199266791-5375a83190b7?w=1200&q=80', 'covers', 'baiser.jpg'),
                'category'     => 'Baiser',
                'status'       => 'published',
                'views_count'  => 904,
                'published_at' => now()->subDays(6),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'Physiologie de la Pénétration : Plaisir, Relaxation Pelvienne et Connexion Somatique',
                'excerpt'      => 'Un guide d’éducation sexuelle positif et scientifique pour comprendre le relâchement des muscles pelviens, le rôle du consentement et la sérénité du corps.',
                'content'      => "Pour que l'intimité physique et la pénétration soient source d'épanouissement et de plaisir, il est fondamental de comprendre le fonctionnement de notre corps. Le bien-être sexuel repose sur l'harmonie entre le cerveau et le système musculaire pelvien.\n\n### Le Rôle Clé du Plancher Pelvien\n\nLe plancher pelvien est un ensemble de muscles qui soutiennent les organes intimes. En situation de stress, d'anxiété ou de douleur, ces muscles ont tendance à se contracter de manière réflexe (comme nous le constatons dans le vaginisme). Pour vivre une pénétration agréable et sans douleur, le mot d'ordre est la **relaxation pelvienne**.\n\n### Techniques pour Favoriser le Relâchement\n\n1. **La Respiration Diaphragmatique** : Une respiration profonde par le ventre permet de détendre naturellement le diaphragme pelvien à chaque implication, abaissant les tensions musculaires.\n2. **La Déconnexion Somatique** : Prendre le temps de masser doucement la zone externe, d'utiliser des lubrifiants de qualité et d'avancer à un rythme extrêmement lent permet au corps d'intégrer des sensations positives de sécurité.\n3. **La Communication Complice** : L'excitation sexuelle et le consentement enthousiaste déclenchent la lubrification naturelle et l'expansion naturelle du canal vaginal (le phénomène de « tente »), rendant l'intimité harmonieuse et confortable.\n\n### Reconnecter avec son Corps\n\nLe plaisir n'est pas une performance. Écouter ses sensations, respecter ses limites et explorer son corps avec douceur sont les bases indispensables d'une vie intime épanouie et sereine.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=1200&q=80', 'covers', 'penetration.jpg'),
                'category'     => 'Penetration',
                'status'       => 'published',
                'views_count'  => 952,
                'published_at' => now()->subDays(7),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'L’Importance du Sexe dans une Relation : Ciment Émotionnel ou Simple Option ?',
                'excerpt'      => 'Au-delà du plaisir physique, la sexualité joue un rôle fondamental de ciment relationnel et de communication au sein du couple. Analyse scientifique.',
                'content'      => "Dans le tumulte du quotidien et les engagements de la vie moderne à Lomé, la place de la sexualité est parfois reléguée au second plan. Pourtant, pour de nombreux couples, elle constitue une composante essentielle de l'harmonie commune.\n\n### Plus qu'une Réaction Physique : Un Vecteur d'Attachement\n\nLors d'une relation intime, le cerveau libère une quantité massive d'**ocytocine**, surnommée à juste titre l'hormone de la confiance et de l'attachement. Cette hormone renforce le sentiment de complicité, réduit instantanément le cortisol (l'hormone du stress) et crée un sentiment profond de sécurité partagée.\n\n### Une Forme de Communication Unique\n\nLa sexualité est un langage non verbal puissant. Elle permet d'exprimer son amour, sa vulnérabilité et son désir d'une manière qu'aucun mot ne peut égaler. Un couple qui maintient une intimité sexuelle saine parvient souvent à surmonter les conflits quotidiens avec plus de résilience et de bienveillance, car le lien de complicité reste profondément vivant.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=1200&q=80', 'covers', 'importance_sexe.jpg'),
                'category'     => 'Amour',
                'status'       => 'published',
                'views_count'  => 872,
                'published_at' => now()->subDays(1),
            ],
            [
                'user_id'      => $author->id,
                'title'        => 'Les Positions dans le Couple : Équilibre, Confort et Complémentarité Somatique',
                'excerpt'      => 'Une exploration bienveillante des différentes postures physiques au sein du couple pour allier confort, détente musculaire et plaisir partagé.',
                'content'      => "Trouver les bonnes postures physiques au sein du couple ne relève pas de la performance, mais de la recherche de l'équilibre mutuel et du confort somatique. Le secret réside dans l'écoute de son corps et le respect de ses limites musculaires.\n\n### Des Positions pour Réduire les Tensions Musculaires\n\nCertaines postures, comme celle de l'« Union latérale » (ou la position de la cuillère), permettent un relâchement total du dos et des muscles pelviens. Elles sont particulièrement recommandées pour les personnes souffrant de tensions ou pour les femmes traversant des périodes d'inconfort gynécologique (comme le vaginisme).\n\n### L'Importance de l'Alignement Corporel\n\nL'utilisation de coussins ergonomiques sous le bas du dos ou les genoux peut transformer l'expérience physique en offrant un soutien optimal et en réduisant la fatigue articulaire. L'essentiel est d'instaurer une communication verbale et somatique ouverte, permettant à chacun de guider le partenaire vers ce qui procure le plus de confort et de sérénité.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1544367567-0f2fcb009e0b?w=1200&q=80', 'covers', 'positions_couple.jpg'),
                'category'     => 'Penetration',
                'status'       => 'published',
                'views_count'  => 934,
                'published_at' => now()->subDays(2),
            ],
            [
                'user_id'      => $admin->id,
                'title'        => 'Fumer ou ne pas fumer ? Choix de Vie, Santé Gynécologique et Bien-être Global',
                'excerpt'      => 'Quels sont les impacts réels du tabac sur notre corps, notre souffle et notre sexualité ? Une analyse objective pour faire des choix éclairés.',
                'content'      => "La question du tabagisme se pose souvent à l'adolescence ou à l'âge adulte. Entre pression sociale, stress quotidien ou simple habitude, le choix de fumer a des répercussions bien plus profondes que le simple geste.\n\n### L'Impact sur le Souffle et l'Énergie\n\nLe tabac réduit la capacité pulmonaire et altère le transport de l'oxygène dans le sang. Au quotidien, cela se traduit par une fatigue plus rapide lors des efforts physiques (et intimes), une peau plus terne et une diminution générale de la vitalité.\n\n### Tabagisme, Sexualité et Santé Gynécologique\n\nSur le plan hormonal et circulatoire, le tabagisme a des effets directs méconnus :\n- **Chez la femme** : Le tabac altère la circulation sanguine dans la zone pelvienne, ce qui peut réduire la lubrification naturelle et accentuer les douleurs menstruelles ou les tensions pelviennes.\n- **Chez l'homme** : La nicotine étant un puissant vasoconstricteur, elle réduit le flux sanguin nécessaire à une érection vigoureuse, augmentant les risques de troubles érectiles.\n\nFaire le choix de ne pas fumer, ou d'entamer une démarche de sevrage, est l'un des cadeaux les plus précieux que l'on puisse s'offrir pour préserver son bien-être global et sa vitalité relationnelle.",
                'cover_image'  => $this->downloadAndStoreImage('https://images.unsplash.com/photo-1527137341206-1d2a6a570077?w=1200&q=80', 'covers', 'fumer_ou_pas.jpg'),
                'category'     => 'Fiction',
                'status'       => 'published',
                'views_count'  => 621,
                'published_at' => now()->subDays(4),
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
            'email'         => 'vianna@example.com',
            'token'         => Str::uuid()->toString(),
            'subscribed_at' => now()->subMonths(1),
        ]);

        Subscriber::create([
            'email'         => 'pierrick@example.com',
            'token'         => Str::uuid()->toString(),
            'subscribed_at' => now()->subWeeks(2),
        ]);

        Subscriber::create([
            'email'         => 'kouassidouffan@gmail.com',
            'token'         => Str::uuid()->toString(),
            'subscribed_at' => now(),
        ]);
    }

    /**
     * Télécharge une image depuis une URL externe, la stocke sur le stockage local et retourne son chemin d'accès.
     */
    private function downloadAndStoreImage(string $url, string $directory, string $filename): string
    {
        $path = "{$directory}/seed/{$filename}";
        
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
            return $path;
        }
        
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(15)->get($url);
            if ($response->successful()) {
                \Illuminate\Support\Facades\Storage::disk('public')->put($path, $response->body());
                return $path;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning("Impossible de télécharger l'image {$url} : " . $e->getMessage());
        }
        
        return $url;
    }
}
