# CAAP — Architecture cible (V19)

## Comptes et autorisations
- Un compte par collège engagé, lié à un identifiant d’établissement stable ; seuls les responsables autorisés peuvent créer et modifier les fiches de leur établissement.
- Un ou plusieurs comptes du groupe académique CAAP avec rôle `reviewer`, habilités à examiner et valider.
- Visiteurs publics : lecture des seules ressources validées.
- Authentification institutionnelle à privilégier, avec gestion des départs et changements de direction.

## Workflow
`draft` → `pending` → `approved` (publié) ; ou `pending` → `changes` → `pending`.
Chaque décision conserve un horodatage, l’identifiant du validateur et un commentaire éventuel. Révision d’une fiche publiée : nouvelle version soumise, l’ancienne reste publiée jusqu’à approbation.

## Modèle de données minimal
`schools(id, name, address, priority, cohort, active)`
`users(id, school_id, role, active)`
`actions(id, school_id, title, domain, school_year, implementation_status, description, review_status, created_at, updated_at, submitted_at, approved_at, approved_by)`
`action_reviews(id, action_id, reviewer_id, decision, comment, created_at)`
`attachments(id, action_id, storage_key, filename, mime_type, approved)`

## Sécurité
Vérification des droits **côté serveur** à chaque requête. Un collège ne peut modifier que ses propres fiches ; seul un validateur peut publier. Les fichiers doivent être contrôlés et stockés dans un espace privé avant approbation. Aucune donnée nominative d’élève ni photographie sans cadre juridique validé. Prévoir sauvegardes, journal d’audit, export CSV/JSON et documentation pour migration vers serveur académique. Validation DPO/DSI avant mise en production.

## Limites de la V19
Prototype HTML sur GitHub Pages : données en `localStorage` propres au navigateur, sélection d’établissement **simulée**, accès à la validation **non protégé**. Aucun compte réel, aucun partage inter-collèges, aucun téléversement de document. Ne pas utiliser pour des données réelles sensibles.
