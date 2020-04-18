<?php

namespace TrainingSourceBundle\Service;

/**
 * Service which main purpouse is to gather some statistics for users
 *
 * @author anonim1133
 */
class UserStats {

    function __construct($entityManager) {
	$this->entityManager = $entityManager;
    }

    /**
     * Returns string with some basic stats explaining user envolvement by tag.
     * @param string $username
     * @param int $tag
     *
     * @return string
     */
    public function get($username, $tag) {

	$sql = '
	SELECT
	    max(name_user) as username,
	    sum(distance) as sum,
	    avg(distance) as avg,
	    max(distance) as max,
	    min(distance) as min
	FROM training
	JOIN training_distance ON training.id= training_distance.training_id
	JOIN distance ON training_distance.distance_id = distance.id
	WHERE
	    name_user = :username AND
	    EXTRACT(WEEK FROM date_add) = EXTRACT(WEEK FROM NOW()) AND
        EXTRACT(YEAR FROM date_add) = EXTRACT(YEAR FROM NOW()) AND 
        id_tag = :tag';

	$stmt = $this->entityManager->getConnection()->prepare($sql);
	$stmt->bindParam('username', $username);
	$stmt->bindParam('tag', $tag);
	$stmt->execute();

	$rows = $stmt->fetchAll();

	if( count($rows) > 0 ) {
	    $rows = $rows[0];
	}

	return $rows;
    }

}
