<?php

namespace WykopBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use WykopBundle\Entity\Training;
use WykopBundle\Entity\Distance;
use WykopBundle\Form\TrainingType;

/**
 * Training controller.
 *
 * @Route("/training")
 */
class TrainingController extends Controller {

    /**
     * Lists all Training entities.
     *
     * @Route("/", name="training")
     * @Method("GET")
     * @Template()
     */
    public function indexAction() {
	$em = $this->getDoctrine()->getManager();

	$entities = $em->getRepository('WykopBundle:Training')->findAll();

	return array(
	    'entities' => $entities,
	);
    }

    /**
     * Creates a new Training entity.
     *
     * @Route("/", name="training_create")
     * @Method("POST")
     * @Template("WykopBundle:Training:new.html.twig")
     */
    public function createAction(Request $request) {
	$em = $this->getDoctrine()->getManager();
	$entity = new Training();
	$form = $this->createCreateForm($entity);
	$form->handleRequest($request);

	$distances = $entity->getDistance();

	if( $this->isFormTrainingValid($form, $distances, $entity->getTag()) ) {

	    $training = new Training();
	    $training->setTag($entity->getTag());
	    $training->setCity($entity->getCity());
	    $training->setNameUser($entity->getNameUser());

	    $training_details_provider = $this->get('getTrainingDetails');

	    //Get last distance
	    $lastDistance = $this->get('LastDistance');

	    try {
		$lastDistance = $lastDistance->get($entity->getTag()->getName());
	    } catch (\Exception $e) {
		$error = new FormError($e->getMessage());
		$form->addError($error);

		return array(
		    'entity' => $entity,
		    'form' => $form->createView(),
		);
	    }

	    $operation = '';

	    if( $training->getTag()->getRound() ) {
		$lastDistance = round($lastDistance);
		$operation = $lastDistance . ' - ';
	    } else {
		$lastDistance = number_format((float) $lastDistance, 2, '.', '');
		$operation = number_format((float) $lastDistance, 2, ',', '') . ' - ';
	    }

	    $result = $lastDistance;

	    $is_stats = false;
	    $entry_content = '';
	    $entry_stats = array();
	    $distanceSum = 0;

	    $km_tags = '';

	    //Deal with multiple trainings in one form
	    foreach ($distances as $index => $dist) {
		$distance = new Distance();

		//Send distance to @getTrainingDetails - retrvie details
		$training_details = $training_details_provider->get($dist);

		//Set details about every distance
		if( count($training_details) > 1 ) {
		    $distance->setLink($dist);
		}

		if( $training->getTag()->getName() === 'plywajzwykopem' && ($training_details['distance'] < 100) )
		    $value_distance = $training_details['distance'] * 1000;
		else
		    $value_distance = $training_details['distance'];


		$value_distance = preg_replace('/[^\.\,0-9]+/', '', $value_distance);
		$value_distance = preg_replace('/[\,]+/', '.', $value_distance);

		if( $training->getTag()->getRound() ) {
		    $value_distance = round($value_distance);
		    $operation .= $value_distance . ' - ';
		    $distanceSum += $value_distance;
		} else {
		    $value_distance = number_format((float) $value_distance, 2, '.', '');
		    $operation .= number_format((float) $value_distance, 2, ',', '') . ' - ';
		    $distanceSum += $value_distance;
		}

		$distance->setDistance($value_distance);

		$result -= $value_distance;

		if( isset($training_details['start_time']) ) {
		    $distance->setStartDate($training_details['start_time']);
		} elseif( isset($entity->getDates()[$index]) ) {
		    $distance->setStartDate($entity->getDates()[$index]);
		} else {
		    $distance->setStartDate(new \DateTime('now'));
		}

		if( isset($training_details['duration']) ) {
		    $distance->setDuration($training_details['duration']);
		    $entry_stats[$index]['duration'] = $training_details['duration'];
		}

		if( isset($training_details['speed_avg']) ) {
		    $is_stats = true;
		    $distance->setAvgSpeed($training_details['speed_avg']);
		    $entry_stats[$index]['speed_avg'] = $training_details['speed_avg'];
		}


		if( isset($training_details['calories']) ) {
		    $distance->setCalories($training_details['calories']);
		    $entry_stats[$index]['calories'] = $training_details['calories'];
		}

		$entry_stats[$index]['distance'] = $value_distance;

		if( isset($training_details['distance_vertical']) ) {
		    $entry_stats[$index]['vertical'] = $training_details['distance_vertical'] . 'm(↑' . $training_details['ascent'] . 'm/↓' . $training_details['descent'] . 'm)';
		}

		if( isset($training_details['heart_rate_avg']) )
		    $entry_stats[$index]['heart_rate_avg'] = $training_details['heart_rate_avg'];

		if( isset($training_details['heart_rate_max']) )
		    $entry_stats[$index]['heart_rate_max'] = $training_details['heart_rate_max'];

		if( isset($training_details['training']) ) {
		    $distance->setDetails($training_details['training']);
		}

		$em->persist($distance);
		$training->setDistance($distance);

		$i = 1;
		while ($tag = (floor($value_distance / ($i * 100)))) {
		    $km_tags .= '#' . ($i * 100) . 'km ';
		    $i++;
		}
	    }

	    $operation = preg_replace('/- $/', '= ', $operation);

	    if( $training->getTag()->getRound() ) {
		$operation .= $result;
	    } else {
		$operation .= number_format((float) $result, 2, ',', '');
	    }

	    //Compile entry content
	    $entry_content .= $operation . "\n\n";

	    $entry_content .= $entity->getDetails() . "\n\n";

	    if( $is_stats )
		$entry_content .= "Statystyki:\n\n";

	    //distance, duration, speed_avg, calories, vertical

	    foreach ($entry_stats as $stats) {
		if( count($stats) > 1 ) {
		    echo "\n";

		    if( isset($stats['distance']) && $training->getTag()->getName() === 'plywajzwykopem' )
			$entry_content .= 'Dystans: ' . $stats['distance'] . " m\n";
		    elseif( isset($stats['distance']) )
			$entry_content .= 'Dystans: ' . $stats['distance'] . " km\n";

		    if( isset($stats['vertical']) )
			$entry_content .= 'Wertykalnie: ' . $stats['vertical'] . "\n";

		    if( isset($stats['duration']) ) {
			$date = new \DateTime();
			$date->setTimestamp($stats['duration']);
			$date->setTimezone(new \DateTimeZone('UTC'));


			$entry_content .= 'Czas: ◷' . $date->format('H:i:s') . "\n";
		    }

		    if( isset($stats['speed_avg']) ) {
			$tmp = 60 / (float) $stats['speed_avg'];
			$tempo = floor($tmp) . ':';

			$tmp = $tmp - floor($tmp);

			$tempo .= str_pad(floor(($tmp) * 60), 2, "0", STR_PAD_LEFT);



			$entry_content .= 'Średnie tempo: ' . $tempo . " min/km\n";
			$entry_content .= 'Średnia prędkość: ' . number_format((float) $stats['speed_avg'], 2, ',', ' ') . " km/h\n";
		    }

		    if( isset($stats['calories']) )
			$entry_content .= 'Kalorie: ' . $stats['calories'] . " kcal\n";

		    if( isset($stats['heart_rate_avg']) )
			$entry_content .= 'Średni puls: ❤' . $stats['heart_rate_avg'] . "bpm\n";

		    if( isset($stats['heart_rate_max']) )
			$entry_content .= 'Maksymalny puls: ❤' . $stats['heart_rate_max'] . "bpm\n";

		    $entry_content .= "\n";
		}
	    }

	    //Set username from session
	    $token = $this->get('security.token_storage')->getToken();

	    $userStats = $this->get('UserStats');
	    $stats = $userStats->get($token->getUsername(), $entity->getTag()->getId());

	    if( !empty($stats) && $training->getTag()->getId() === 1 ) {
		$entry_content .= PHP_EOL . 'W tym tygodniu to już ' . ($stats['sum'] + $distanceSum) . 'km!' . PHP_EOL;
	    }

	    $entry_content .= '#' . $entity->getTag()->getName() . " ";

	    $city = $entity->getCity();
	    if( !is_null($city) )
		$entry_content .= '#rusz' . $city->getName();

	    if( $entity->getTag()->getName() == 'rowerowyrownik' ) {
		$entry_content .= ' ' . $km_tags;
	    }

	    if( $entity->getAd() == true ) {
		$entry_content .= "\n\n" . 'Wpis dodany za pomocą [tego skryptu](' . $this->container->getParameter('app_url') . ')'
			. "\n" . '!Najlepszy, bo darmowy'
			. "\n" . '!Jest do wszystkiego więc... Jest dobry!'
			. "\n" . '!Samo liczy, to chyba magia'
			. "\n" . '!Skrypt się nie myli, to inni się mylą'
			. "\n" . '!Będą z tego ładne wykresiki'
			. "\n" . '!Powiedzcie mamie, powiedzcie babci, niech odejmują!';
	    }

	    $training->setNameUser($token->getUsername());

	    if( $token->getAttribute('wykop_sex') == 'male' || $token->getAttribute('wykop_sex') == 'female' )
		$training->setSexUser($token->getAttribute('wykop_sex'));
	    else
		$training->setSexUser(null);

	    $training->setDetails($entry_content);

	    //Subtract distances, build operation
	    //Compile new entry
	    //Send new entry to Wykop
	    $wykop = $this->get('WykopApi');
	    $wykop->setUserKey($token->getCredentials());
	    $result = $wykop->doRequest('Entries/Add', array('body' => $entry_content, 'embed' => $entity->getEmbed()));

	    if( $wykop->isValid() ) {
		$em->persist($training);
		$em->flush();
		return $this->redirect('https://wykop.pl/wpis/' . (int) $result['id']);
	    } else {
		$error = new FormError('Wykop: ' . $wykop->getError());
		$form->addError($error);
	    }
	}

	return array(
	    'entity' => $entity,
	    'form' => $form->createView(),
	);
    }

    /**
     * Creates a form to create a Training entity.
     *
     * @param Training $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Training $entity, $ad = false, $default_tag = 0, $default_city = 0) {
	$form = $this->createForm(new TrainingType($ad, $default_tag, $default_city), $entity, array(
	    'action' => $this->generateUrl('training_create'),
	    'method' => 'POST',
	));

	$form->add('submit', 'submit', array(
	    'label' => 'Dodaj',
	    'attr' => array(
		'class' => 'btn',
		'onClick' => 'addEntry()'
	    )
	));

	return $form;
    }

    /**
     * Displays a form to create a new Training entity.
     *
     * @Route("/new", name="training_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction(Request $request) {
	$entity = new Training();

	$default_tag = 0;
        $em = $this->getDoctrine()->getManager();
	if( !is_null($request->cookies->get('default_tag')) ) {
	    $default_tag = $em->getReference("WykopBundle:Tag", (int)$request->cookies->get('default_tag'));
	}else{
        $default_tag = $em->getReference("WykopBundle:Tag", (int)$request->query->get('tag', 0));
    }

	$default_city = 0;

	if( !is_null($request->cookies->get('default_city')) ) {
	    $default_city = $em->getReference("WykopBundle:City", (int)$request->cookies->get('default_city'));
	}else{
        $default_city = $em->getReference("WykopBundle:City", (int)$request->query->get('city', 0));
    }

	$ad = 'false';

	if( !is_null($request->cookies->get('ad')) ) {
	    $ad = $request->cookies->get('ad');
	} else {
	    $ad = 'true';
	}

	$form = $this->createCreateForm($entity, $ad, $default_tag, $default_city);

	return array(
	    'entity' => $entity,
	    'form' => $form->createView(),
	);
    }

    /**
     * Finds and displays a Training entity.
     *
     * @Route("/{id}", name="training_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id) {
	$em = $this->getDoctrine()->getManager();

	$entity = $em->getRepository('WykopBundle:Training')->find($id);

	if( !$entity ) {
	    throw $this->createNotFoundException('Unable to find Training entity.');
	}

	$deleteForm = $this->createDeleteForm($id);

	return array(
	    'entity' => $entity,
	    'delete_form' => $deleteForm->createView(),
	);
    }

    /**
     * Displays a form to edit an existing Training entity.
     *
     * @Route("/{id}/edit", name="training_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id) {
	$em = $this->getDoctrine()->getManager();

	$entity = $em->getRepository('WykopBundle:Training')->find($id);

	if( !$entity ) {
	    throw $this->createNotFoundException('Unable to find Training entity.');
	}

	$editForm = $this->createEditForm($entity);
	$deleteForm = $this->createDeleteForm($id);

	return array(
	    'entity' => $entity,
	    'edit_form' => $editForm->createView(),
	    'delete_form' => $deleteForm->createView(),
	);
    }

    /**
     * Creates a form to edit a Training entity.
     *
     * @param Training $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(Training $entity) {
	$form = $this->createForm(new TrainingType(), $entity, array(
	    'action' => $this->generateUrl('training_update', array('id' => $entity->getId())),
	    'method' => 'PUT',
	));

	$form->add('submit', 'submit', array('label' => 'Update'));

	return $form;
    }

    /**
     * Edits an existing Training entity.
     *
     * @Route("/{id}", name="training_update")
     * @Method("PUT")
     * @Template("WykopBundle:Training:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
	$em = $this->getDoctrine()->getManager();

	$entity = $em->getRepository('WykopBundle:Training')->find($id);

	if( !$entity ) {
	    throw $this->createNotFoundException('Unable to find Training entity.');
	}

	$deleteForm = $this->createDeleteForm($id);
	$editForm = $this->createEditForm($entity);
	$editForm->handleRequest($request);

	if( $editForm->isValid() ) {
	    $em->flush();

	    return $this->redirect($this->generateUrl('training_edit', array('id' => $id)));
	}

	return array(
	    'entity' => $entity,
	    'edit_form' => $editForm->createView(),
	    'delete_form' => $deleteForm->createView(),
	);
    }

    /**
     * Deletes a Training entity.
     *
     * @Route("/{id}", name="training_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id) {
	$form = $this->createDeleteForm($id);
	$form->handleRequest($request);

	if( $form->isValid() ) {
	    $em = $this->getDoctrine()->getManager();
	    $entity = $em->getRepository('WykopBundle:Training')->find($id);

	    if( !$entity ) {
		throw $this->createNotFoundException('Unable to find Training entity.');
	    }

	    $em->remove($entity);
	    $em->flush();
	}

	return $this->redirect($this->generateUrl('training'));
    }

    /**
     * Creates a form to delete a Training entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
	return $this->createFormBuilder()
			->setAction($this->generateUrl('training_delete', array('id' => $id)))
			->setMethod('DELETE')
			->add('submit', 'submit', array('label' => 'Delete'))
			->getForm()
	;
    }

    private function isFormTrainingValid($form, $distances, $tag) {

	$valid = false;

	if( count($distances) <= 0 ) {
	    $error = new FormError("Musisz podać co najmniej jeden wynik");
	    $form->get('distance')->addError($error);
	} else {
	    $valid = true;
	}

	foreach ($distances as $distance)
	    if( $distance == 0 && preg_match('/(strava)|(endomondo)|(http)/', $distance) == 0 ) {
		$valid = false;

		$error = new FormError("Musisz podać co najmniej jeden wynik, każdy musi być większy od 0");
		$form->get('distance')->addError($error);
	    }

	if( is_null($tag) ) {
	    $error = new FormError("Musisz wybrać tag");
	    $form->get('Tag')->addError($error);

	    $valid = false;
	}

	return $form->isValid() && $valid;
    }

}
