<?php

namespace App\Controller;

use App\Entity\Card;
use App\Entity\Deck;
use App\Entity\State;
use App\Entity\Comment;
use App\Form\CommentType;
use App\Entity\Composition;
use App\Repository\CardRepository;
use App\Repository\DeckRepository;
use App\Repository\StateRepository;
use App\Repository\FormatRepository;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\CompositionRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DeckBuilderController extends AbstractController
{
    // Constructeur de la classe, injecte le service HttpClientInterface
    public function __construct(private HttpClientInterface $httpClient) {}
    
    #[Route('/build/deck/{id}/{state}', name: 'app_deck_builder')]
    public function deckBuild(Deck $deck, String $state, DeckRepository $deckRepository, CompositionRepository $compositionRepository, StateRepository $stateRepository, CommentRepository $commentRepository, EntityManagerInterface $entityManager, Request $request): Response
    {

        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);

        $stateMain = $stateRepository->findOneBy(['stateName' => "Mainboard"]);
        $stateSide = $stateRepository->findOneBy(['stateName' => "Sideboard"]);
        $stateMaybe = $stateRepository->findOneBy(['stateName' => "Maybeboard"]);
        $stateToken = 'Main';

        $compositionMain = $compositionRepository->findByState($deck, $stateMain);
        $compositionSide = $compositionRepository->findByState($deck, $stateSide);
        $compositionMaybe = $compositionRepository->findByState($deck, $stateMaybe);

        // Initialize arrays to store counts for card types, subtypes, and colors.
        $typeCount = [];
        $subtypeCount = [];
        $colorCount = [];
        $cmcCount = []; // Initialize an empty array to hold CMC counts
        $count = 0;

        // Process each card in the Mainboard composition.
        foreach ($compositionMain as $el) {

            $card = $el->getCard(); // The card entity is associated with the composition
            $quantity = $el->getQuantity();
            
            // Increment card count based on quantity.
            $count += 1 * $quantity;

            $cardData = $card->getData();

            // Extract types, subtypes, and colors 
            // Process and count card types.
            $validTypes = ['creature', 'artifact', 'instant', 'sorcery', 'planeswalker', 'battle', 'land', 'enchantment'];

            if (isset($cardData['type_line'])) {
                // Split the type_line by spaces 
                $types = explode(' ', strtolower($cardData['type_line'])); // Convert to lowercase 

                foreach ($types as $type) {
                    // Only count the type if it's in the list of valid types
                    if (in_array($type, $validTypes)) {
                        if (!isset($typeCount[$type])) {
                            $typeCount[$type] = 0;
                        }
                        $typeCount[$type] += $quantity; // Add the card's quantity to the count
                    }
                }
            }
            
// Tableau des noms de terrains de base en minuscule
$basicLands = ['forest', 'plains', 'island', 'swamp', 'mountain'];

// Vérifie si la clé 'type_line' est présente dans $cardData
if (isset($cardData['type_line'])) {
    
    // Vérifie si la chaîne contient le caractère "—" (utilisé pour séparer les sous-types dans Magic: The Gathering)
    if (strpos($cardData['type_line'], '—') !== false) {
        
        // Divise la chaîne à "—", et récupère les sous-types après ce caractère
        // La première partie avant "—" est ignorée (d'où la liste vide avant la virgule)
        list(, $subtypes) = explode('—', $cardData['type_line']);
        
        // Sépare les sous-types en mots individuels et enlève les espaces inutiles
        $subtypes = explode(' ', trim($subtypes));

        // Parcourt chaque sous-type trouvé
        foreach ($subtypes as $subtype) {
            
            // Vérifie si le sous-type n'est pas un type valide défini dans $validTypes et qu'il n'est pas un terrain de base
            if (!in_array(strtolower($subtype), $validTypes) && !in_array(strtolower($subtype), $basicLands)) {
                
                // Si le sous-type n'est pas déjà dans $subtypeCount, initialise son compteur à 0
                if (!isset($subtypeCount[$subtype])) {
                    $subtypeCount[$subtype] = 0;
                }
                
                // Ajoute la quantité de la carte au compteur du sous-type
                $subtypeCount[$subtype] += $quantity;
            }
        }
    }
}
            
        // Traite et compte les couleurs d'identité de la carte.
        if (isset($cardData['color_identity'])) {
            
            // Parcourt chaque couleur dans le tableau 'color_identity' de la carte
            foreach ($cardData['color_identity'] as $color) {
                
                // Si la couleur n'a pas encore été rencontrée, initialise son compteur à 0
                if (!isset($colorCount[$color])) {
                    $colorCount[$color] = 0;
                }
                
                // Ajoute la quantité de la carte au compteur de la couleur correspondante
                $colorCount[$color] += $quantity;
            }
        }

            // Check if the card data contains CMC information
            if (isset($cardData['cmc'])) {
                $cmc = (int) $cardData['cmc']; // Convert the CMC to an integer

                // Ensure there's an entry in the array for this CMC, starting from 0 upwards
                if (!isset($cmcCount[$cmc])) {
                    $cmcCount[$cmc] = 0; // Initialize the count for this specific CMC if not already set
                }

                // Add the card's quantity to the count for this CMC
                $cmcCount[$cmc] += $quantity;
            }
        }

        $deck->setColorCount($colorCount);
        $entityManager->persist($deck);
        $entityManager->flush();

        $comments = $commentRepository->findBy(['deck' => $deck]);
        $comment = new Comment();
        $form = $this->createForm(CommentType::class);

        // Handle the request
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();
            $comment->setUser($user);
            $comment->setCreationDate(new \DateTime()); // Set current date and time
            $comment->setDeck($deck);

            $formData = $form->getData();
            $textContent = $formData->getTextContent();

            $comment->setTextContent($textContent);

            $entityManager->persist($user);
            $entityManager->persist($deck);
            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']); 
        }

        if($state == 'Mainboard') {

            return $this->render('decks/deckBuilder.html.twig', [
                'deck' => $deck,
                'composition' => $compositionMain,
                'compositionSide' => $compositionSide,
                'compositionMaybe' => $compositionMaybe,
                'count' => $count,
                'stateToken' => $stateToken,
                'comments' => $comments,
                'commentForm' => $form,
                'comment' => $comment,
                'typeCount' => $typeCount, // Passe le décompte des types de cartes
                'subtypeCount' => $subtypeCount, // Passe le décompte des sous-types de cartes
                'colorCount' => $colorCount, // Passe le décompte des couleurs des cartes
                'cmcCount' => $cmcCount, // Passe le décompte du coût converti de mana (CMC) des cartes
            ]);

        } else if ($state == 'Sideboard') {

            $stateToken = 'Side';

            return $this->render('decks/deckBuilder.html.twig', [
                'deck' => $deck,
                'composition' => $compositionSide,
                'compositionSide' => $compositionMain,
                'compositionMaybe' => $compositionMaybe,
                'count' => $count,
                'stateToken' => $stateToken,
                'comments' => $comments,
                'commentForm' => $form,
                'comment' => $comment,
                'typeCount' => $typeCount, // Pass types count
                'subtypeCount' => $subtypeCount, // Pass subtypes count
                'colorCount' => $colorCount, // Pass colors count
                'cmcCount' => $cmcCount
            ]);

        } else if ($state == 'Maybeboard') {

            $stateToken = 'Maybe';

            return $this->render('decks/deckBuilder.html.twig', [
                'deck' => $deck,
                'composition' => $compositionMaybe,
                'compositionSide' => $compositionMain,
                'compositionMaybe' => $compositionSide,
                'count' => $count,
                'stateToken' => $stateToken,
                'comments' => $comments,
                'commentForm' => $form,
                'comment' => $comment,
                'typeCount' => $typeCount, // Pass types count
                'subtypeCount' => $subtypeCount, // Pass subtypes count
                'colorCount' => $colorCount, // Pass colors count
                'cmcCount' => $cmcCount
            ]);

        } else {

            return $this->render('decks/deckBuilder.html.twig', [
                'deck' => $deck,
                'composition' => $compositionMain,
                'compositionSide' => $compositionSide,
                'compositionMaybe' => $compositionMaybe,
                'count' => $count,
                'stateToken' => $stateToken,
                'comments' => $comments,
                'commentForm' => $form,
                'comment' => $comment,
                'typeCount' => $typeCount, // Pass types count
                'subtypeCount' => $subtypeCount, // Pass subtypes count
                'colorCount' => $colorCount, // Pass colors count
                'cmcCount' => $cmcCount
            ]);
        }
    }

    #[Route('/user/{user}/deck/{deck}/commander', name: 'save_commander_deck', methods: ['POST'])]
    public function saveCommander(Deck $deck, EntityManagerInterface $entityManager, DeckRepository $deckRepository, Request $request): Response 
    {
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $currentDate = new \DateTime();
        $deck->setUpdateDate($currentDate);
    
        $data = $request->get('cardData');
        $dataJS = json_decode($data, true); // true pour récupérer un tableau

        $deck->setCommander($dataJS);
        $entityManager->persist($deck);
        $entityManager->flush();

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }

    #[Route('/user/{user}/deck/{deck}/delete-commander', name: 'delete_commander', methods: ['POST', 'GET'])]
    public function deleteCommander(Deck $deck, EntityManagerInterface $entityManager, DeckRepository $deckRepository): Response 
    {

        //ajouter une condition si le user en session est le même que le user du deck
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $currentDate = new \DateTime();
        $deck->setUpdateDate($currentDate);
        $deck->setCommander(null);

        $entityManager->persist($deck);
        $entityManager->flush();
        

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }



    #[Route('/user/{user}/deck/{deck}/{card}/{state}/plus', name: 'plus_card_deck', methods: ['POST', 'GET'])]
    public function plusOne(Deck $deck, $card, String $state, DeckRepository $deckRepository, CardRepository $cardRepository, CompositionRepository $compositionRepository, StateRepository $stateRepository, EntityManagerInterface $entityManager, Request $request, ) {

        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $currentDate = new \DateTime();
        $deck->setUpdateDate($currentDate);

        $card = $cardRepository->findOneBy(['scryfallId' => $card]);

        $compositionState = $stateRepository->findOneBy(['stateName' => $state]);
        $composition = $compositionRepository->findOneBy(['deck' => $deck, 'card' => $card, 'state' => $compositionState]);

        $quantity = $composition->getQuantity();
        $quantity += 1;
        $composition->setQuantity($quantity);
        $entityManager->persist($composition);
        $entityManager->flush();

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }

    #[Route('/user/{user}/deck/{deck}/{card}/{state}/minus', name: 'minus_card_deck', methods: ['POST', 'GET'])]
    public function minusOne(Deck $deck, $card, String $state, StateRepository $stateRepository, DeckRepository $deckRepository, CardRepository $cardRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager) {

        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $currentDate = new \DateTime();
        $deck->setUpdateDate($currentDate);


        $card = $cardRepository->findOneBy(['scryfallId' => $card]);

        $compositionState = $stateRepository->findOneBy(['stateName' => $state]);
        $composition = $compositionRepository->findOneBy(['deck' => $deck, 'card' => $card, 'state' => $compositionState]);

        $quantity = $composition->getQuantity();

        if($quantity > 1) {
            $quantity -= 1;
            $composition->setQuantity($quantity);
            $entityManager->persist($composition);

        } else if ($quantity == 1) {
            $entityManager->remove($composition);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }


    #[Route('/user/{user}/deck/{deck}/{card}/{state}/delete', name: 'delete_card_deck', methods: ['GET'])]
    public function deleteCardFromDeck(Deck $deck, $card, String $state, StateRepository $stateRepository, DeckRepository $deckRepository, CardRepository $cardRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager) {
        
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $card = $cardRepository->findOneBy(['scryfallId' => $card]);

        $compositionState = $stateRepository->findOneBy(['stateName' => $state]);
        $composition = $compositionRepository->findOneBy(['deck' => $deck, 'card' => $card, 'state' => $compositionState]);

        $entityManager->remove($composition);
        $entityManager->flush();

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }

    #[Route('/user/{user}/deck/{deck}/deleteAllCards', name: 'delete_all_cards_deck', methods: ['GET'])]
    public function deleteAllCards(Deck $deck, DeckRepository $deckRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager) {
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);

        $compositions = $compositionRepository->findBy(['deck' => $deck]);

        foreach($compositions as $composition) {
            $entityManager->remove($composition);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_deck_builder', ['id' => $deck->getId(), 'state' => 'Mainboard']);
    }

    #[Route('/user/{user}/deck/{deck}/delete', name: 'delete_deck', methods: ['GET'])]
    public function deleteDeck(Deck $deck, DeckRepository $deckRepository, CompositionRepository $compositionRepository, EntityManagerInterface $entityManager, UserInterface $user) {

        $user = $this->getUser();
        $userId = $user->getId();
        $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
        $compositions = $compositionRepository->findBy(['deck' => $deck]);

        foreach($compositions as $composition) {
            $entityManager->remove($composition);
        }

        $entityManager->remove($deck);
        $entityManager->flush();

        return $this->redirectToRoute('app_deck_manager', ['id' => $userId]);

    }


#[Route('/user/{user}/deck/{deck}/{state}/card', name: 'save_card_deck', methods: ['POST'])]
public function saveCard(
    Deck $deck, 
    ?Card $card = null, 
    string $state, 
    StateRepository $stateRepository, 
    EntityManagerInterface $entityManager, 
    DeckRepository $deckRepository, 
    CompositionRepository $compositionRepository, 
    Request $request
): JsonResponse 
{
    // Fetch deck and update timestamp
    $deck = $deckRepository->findOneBy(['id' => $deck->getId()]);
    $deck->setUpdateDate(new \DateTime());

    // Retrieve card ID and card data from the request
    $cardId = $request->get('cardId');
    $cardData = $request->get('cardData'); // Expecting JSON from request
    
    // Ensure state is fetched correctly
    $stateEntity = $stateRepository->findOneBy(['stateName' => $state]);

    // Convert cardData to an array if it's not already
    $cardDataArray = json_decode($cardData, true); // If cardData is passed as a JSON string

    // Use the processCard method to handle card saving logic
    $this->processCard($cardDataArray, $deck, 1, $stateEntity, $compositionRepository, $entityManager);

    // Flush to save changes to the database
    $entityManager->flush();

    return new JsonResponse(['message' => 'La carte a été ajoutée avec succès!']);
}

public function saveCardToDeck(Deck $deck, Card $card = null, Composition $composition = null, int $quantity, State $state, EntityManagerInterface $entityManager, CardRepository $cardRepository): void {

    $composition = $composition ?? new Composition();
    $composition->setDeck($deck);
    $composition->setCard($card);
    $composition->setQuantity($quantity + ($composition->getQuantity() ?? 0)); // Update quantity
    $composition->setState($state);
    
    $entityManager->persist($composition);

}


#[Route('/user/{user}/deck/import', name: 'import_deck_txt', methods: ['POST'])]
public function importDeck(
    StateRepository $stateRepository,
    CompositionRepository $compositionRepository,
    FormatRepository $formatRepository,
    EntityManagerInterface $entityManager,
    Request $request
): Response {
    $deckForm = $request->get('import_deck_form');
    $deckName = $deckForm['deckName'];
    
    // Create the deck entity
    $deck = new Deck();
    $deckFormat = $formatRepository->findOneBy(['formatName' => 'Commander / EDH']);
    $state = $stateRepository->findOneBy(['stateName' => 'MainBoard']);
    $deck->setCreationDate(new \DateTime());
    $deck->setFormat($deckFormat);
    $deck->setStatus(true);
    $deck->setDeckName($deckName);
    $deck->setUser($this->getUser());

    //handle deckformat

    $entityManager->persist($deck);
    
    // Process the deck list
    $deckList = $deckForm['deckList'];
    $lines = explode("\n", $deckList);
    $notFound = [];
    $addedCards = 0;
    $submittedCards = 0;

    // First, aggregate the quantities for each card
    foreach ($lines as $line) {
        $submittedCards++;
        if (preg_match('/^(\d+)\s+(.+)$/', trim($line), $matches)) {
            $quantity = (int) $matches[1];
            $cardName = $matches[2];

            // Aggregate card quantities
            if (isset($cardQuantities[$cardName])) {
                $cardQuantities[$cardName] += $quantity;
            } else {
                $cardQuantities[$cardName] = $quantity;
            }
        }
    }

    // traitement de la liste agrégée de cartes
    foreach ($cardQuantities as $cardName => $quantity) {
        // Récupération de  la carte depuis l'API Scryfall
        $cardData = $this->fetchCardFromScryfall($cardName);

        if ($cardData) {
            // Traitetement l'enregistrement de la carte dans le deck
            $this->processCard($cardData, $deck, $quantity, $state, $compositionRepository, $entityManager);
            $addedCards++; // Incrémenter le compteur de cartes ajoutées
        } else {
            $notFound[] = $cardName; // Ajouter le nom de la carte non trouvée si non trouvée, ou problème
        }
    }

    // Persist all changes
    $entityManager->flush();

    // Return a response (or render a view if needed)
    return $this->json([
        'statut' => 'Succès ! L\'importation a réussi ! Recharger la page pour que le deck apparaisse !',
        'cartes ajoutées' => $addedCards,
        'non_trouvées' => $notFound,
        'non_ajoutées' => $submittedCards - $addedCards,
        'total_soumis' => $submittedCards
    ]);
}

/**
 * Process a card by either creating or updating its composition in the deck.
 */
private function processCard(
    array $cardData,
    Deck $deck,
    int $quantity,
    State $state,
    CompositionRepository $compositionRepository,
    EntityManagerInterface $entityManager
): void {
    // Find or create the card
    $card = $this->findOrCreateCard($cardData, $entityManager);
    
    // Check if composition already exists
    $composition = $compositionRepository->findOneBy([
        'deck' => $deck,
        'card' => $card,
        'state' => $state
    ]);

    // If composition exists, update quantity, else create a new composition
    if ($composition) {
        $composition->setQuantity($composition->getQuantity() + $quantity);
    } else {
        $composition = new Composition();
        $composition->setDeck($deck);
        $composition->setCard($card);
        $composition->setQuantity($quantity);
        $composition->setState($state);
        $entityManager->persist($composition);
    }
}

/**
 * Find a card by Scryfall ID or create a new one if it does not exist.
 */
private function findOrCreateCard(array $cardData, EntityManagerInterface $entityManager): Card
{
    $cardRepository = $entityManager->getRepository(Card::class);
    $card = $cardRepository->findOneBy(['scryfallId' => $cardData['id']]);

    if (!$card) {
        $card = new Card();
        $card->setScryfallId($cardData['id']);
        $card->setData($cardData); // Ensure $cardData is an array
        $entityManager->persist($card);
    }

    return $card;
}

/**
 * Récupérer les données de la carte depuis l'API Scryfall par le biais du HttpClient de Symfony
 */
private function fetchCardFromScryfall(string $cardName): ?array
{
    try {
        // Ajouter un délai pour éviter de surcharger l'API
        usleep(1000000); // Délai de 1 seconde

        // Envoyer une requête GET à l'API Scryfall avec le nom exact de la carte
        $response = $this->httpClient->request('GET', 'https://api.scryfall.com/cards/named', [
            'query' => ['exact' => $cardName]
        ]);

        // Vérifier si la réponse a un statut 200 (succès)
        if ($response->getStatusCode() === 200) {
            return $response->toArray(); // Convertir la réponse JSON en tableau
        }
    } catch (\Exception $e) {
        // Consigner l'erreur ou la gérer selon les besoins
    }

    return null; // Retourner null si la carte n'est pas trouvée ou en cas de problème
}

  


}

        //TODO deck (exceptions commandant, affichages etc) -> systeme pour changer les fetch sur la recherche -> ajouter la recherche générale -> trouver un moyen d'afficher correctement les carte doubles faces -> boutons pour ajouter/supprimer une carte au deck

        //TODO affichage double cartes sur toutes les vues : cardDetail

        //TODO bug quand register -> utilisateur non connecté
