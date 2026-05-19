DELETE FROM comments;
INSERT INTO comments (article_id, user_id, content, is_approved, created_at, updated_at) VALUES
(1, 4, 'Les Nana Benz sont une vraie fierte africaine ! Merci pour cet article qui met en lumiere leur genie economique et leur courage hors du commun.', 1, datetime('now', '-2 days'), datetime('now', '-2 days')),
(1, 5, 'Je ne savais pas que ces femmes avaient autant d influence dans l economie regionale. Tres enrichissant, bravo !', 1, datetime('now', '-1 day'), datetime('now', '-1 day')),
(1, 6, 'Mon arriere-grand-mere me parlait souvent des Nana Benz. Cet article m a beaucoup appris. Merci !', 1, datetime('now', '-6 hours'), datetime('now', '-6 hours')),
(2, 5, 'Le fufu de ma maman me manque ! Cet article m a donne une vraie nostalgie. La gastronomie togolaise merite d etre connue dans le monde entier.', 1, datetime('now', '-3 days'), datetime('now', '-3 days')),
(2, 7, 'Excellente presentation de notre cuisine ! J aimerais voir un article sur la preparation du deguee ou du beignet de haricot.', 1, datetime('now', '-2 days'), datetime('now', '-2 days')),
(2, 8, 'Le pinon est mon plat prefere. Tres bien explique dans cet article, on a presque l impression d y etre. Bravo !', 1, datetime('now', '-12 hours'), datetime('now', '-12 hours')),
(3, 4, 'Kpalime est magnifique ! J y suis alle l annee derniere pour les chutes d Akloa, un vrai coup de coeur. Merci pour ce beau rappel.', 1, datetime('now', '-4 days'), datetime('now', '-4 days')),
(3, 6, 'Article tres bien documente. Le Togo regorge de beautes naturelles sous-estimees. Ce genre de contenu aide vraiment a valoriser notre pays.', 1, datetime('now', '-1 day'), datetime('now', '-1 day')),
(4, 7, 'Je suis vegane depuis 2 ans et j adore l idee d adapter nos plats traditionnels. Super article !', 1, datetime('now', '-5 days'), datetime('now', '-5 days')),
(4, 8, 'Tres bonne initiative ! Il faut montrer que le veganisme n est pas reserve aux occidentaux. Nos traditions culinaires sont naturellement riches en vegetal.', 1, datetime('now', '-3 days'), datetime('now', '-3 days')),
(5, 5, 'Le sexe dans une relation c est bien plus qu un acte physique. Article mature et bien ecrit, j apprecie la franchise du sujet.', 1, datetime('now', '-1 day'), datetime('now', '-1 day')),
(5, 4, 'Sujet souvent tabou mais tellement important pour la sante du couple. Merci d avoir aborde ca avec sensibilite.', 1, datetime('now', '-8 hours'), datetime('now', '-8 hours')),
(6, 6, 'Article courageux. La communication est la cle de tout dans une relation. J aurais aime plus d exemples concrets mais c est deja tres bien.', 1, datetime('now', '-2 days'), datetime('now', '-2 days')),
(7, 8, 'Je fume depuis 10 ans et cet article m a vraiment fait reflechir. Les arguments sont clairs et sans jugement. Merci !', 1, datetime('now', '-3 days'), datetime('now', '-3 days')),
(7, 7, 'Il manque peut-etre une partie sur les cigarettes electroniques qui sont de plus en plus repandues chez les jeunes.', 1, datetime('now', '-1 day'), datetime('now', '-1 day'));
