<?php

class TripMarker extends Controller {
	private $db;
	private $regUserModel;
	private $featureFlagsCache;

	public function __construct() {
		$this->db = new Database();
		$this->regUserModel = $this->model('RegUserModel');
		$this->featureFlagsCache = null;
	}

	public function ongoingTrip($tripId = null) {
		$userId = $this->getAuthenticatedUserId();
		if (!$userId) {
			header('Location: ' . URL_ROOT . '/user/login');
			return;
		}

		$tripId = (int)$tripId;
		if ($tripId <= 0) {
			header('Location: ' . URL_ROOT . '/RegUser/trips');
			return;
		}

		$this->regUserModel->syncTripLifecycleForUser($userId, $tripId);

		$flags = $this->getFeatureFlags();
		$trip = $this->fetchTripForUser($userId, $tripId, $flags);

		if (!$trip) {
			header('Location: ' . URL_ROOT . '/RegUser/trips');
			return;
		}

		if (!$this->isTripOngoing($trip)) {
			header('Location: ' . URL_ROOT . '/RegUser/tripEventList/' . $tripId);
			return;
		}

		$datePills = $this->buildDatePills($trip['startDate'] ?? null, $trip['endDate'] ?? null);

		ob_start();
		$this->view('Trips/ongoingTrip', [
			'trip' => $trip,
			'datePills' => $datePills
		]);
		$html = ob_get_clean();

		$loadingContent = [
			'html' => $html,
			'css' => URL_ROOT . '/public/css/regUser/trips/ongoingTrip.css',
			'js' => URL_ROOT . '/public/js/regUser/trips/ongoingTrip.js'
		];

		$this->view('UserTemplates/travellerDash', [
			'tabId' => 'trips',
			'loadingContent' => $loadingContent
		]);
	}

	public function tripSnapshot($tripId = null) {
		$userId = $this->getAuthenticatedUserId();
		if (!$userId) {
			$this->respondJson(['success' => false, 'message' => 'Please log in first.'], 401);
			return;
		}

		$tripId = (int)$tripId;
		if ($tripId <= 0) {
			$this->respondJson(['success' => false, 'message' => 'Invalid trip ID.'], 400);
			return;
		}

		$this->regUserModel->syncTripLifecycleForUser($userId, $tripId);

		$flags = $this->getFeatureFlags();
		$trip = $this->fetchTripForUser($userId, $tripId, $flags);

		if (!$trip) {
			$this->respondJson(['success' => false, 'message' => 'Trip not found.'], 404);
			return;
		}

		if (!$this->isTripOngoing($trip)) {
			$this->respondJson([
				'success' => false,
				'message' => 'This trip is not in ongoing state yet.'
			], 409);
			return;
		}

		$startPinGenerated = false;
		if (!empty($flags['hasStartPin']) && $this->shouldRequireStartPin($trip) && empty($trip['startPin'])) {
			$generatedPin = $this->generateAndStoreStartPin($userId, $tripId, $flags);
			if ($generatedPin !== null) {
				$trip['startPin'] = $generatedPin;
				$startPinGenerated = true;
			}
		}

		$tripPayload = [
			'tripId' => (int)($trip['tripId'] ?? $tripId),
			'tripTitle' => $trip['tripTitle'] ?? 'Ongoing Trip',
			'description' => $trip['description'] ?? '',
			'startDate' => $trip['startDate'] ?? null,
			'endDate' => $trip['endDate'] ?? null,
			'status' => $trip['status'] ?? 'ongoing',
			'startPinRequired' => $this->shouldRequireStartPin($trip),
			'startPinGenerated' => $startPinGenerated,
			'startPin' => !empty($flags['hasStartPin']) ? ($trip['startPin'] ?? null) : null,
			'pinMatch' => !empty($flags['hasPinMatch']) ? (int)($trip['pinMatch'] ?? 0) : 0
		];

		$events = $this->getTripEventCards($userId, $tripId, $flags);

		$this->respondJson([
			'success' => true,
			'trip' => $tripPayload,
			'featureFlags' => $flags,
			'events' => $events
		]);
	}

	public function getEventCardsByDate($tripId = null, $eventDate = null) {
		$userId = $this->getAuthenticatedUserId();
		if (!$userId) {
			$this->respondJson(['success' => false, 'message' => 'Please log in first.'], 401);
			return;
		}

		$tripId = (int)$tripId;
		$eventDate = $this->normalizeDate($eventDate);

		if ($tripId <= 0 || !$eventDate) {
			$this->respondJson(['success' => false, 'message' => 'Invalid trip or date.'], 400);
			return;
		}

		$this->regUserModel->syncTripLifecycleForUser($userId, $tripId);

		$flags = $this->getFeatureFlags();
		$trip = $this->fetchTripForUser($userId, $tripId, $flags);

		if (!$trip) {
			$this->respondJson(['success' => false, 'message' => 'Trip not found.'], 404);
			return;
		}

		if (!$this->isTripOngoing($trip)) {
			$this->respondJson(['success' => false, 'message' => 'Trip is not ongoing.'], 409);
			return;
		}

		$eventCards = $this->getTripEventCards($userId, $tripId, $flags, $eventDate);
		$coordinates = $this->buildCoordinatesForEvents($eventCards);

		$this->respondJson([
			'success' => true,
			'eventCards' => $eventCards,
			'coordinates' => $coordinates
		]);
	}

	public function getEventCoordinates($tripId = null, $eventDate = null) {
		$userId = $this->getAuthenticatedUserId();
		if (!$userId) {
			$this->respondJson(['success' => false, 'message' => 'Please log in first.'], 401);
			return;
		}

		$tripId = (int)$tripId;
		$eventDate = $this->normalizeDate($eventDate);

		if ($tripId <= 0 || !$eventDate) {
			$this->respondJson(['success' => false, 'message' => 'Invalid trip or date.'], 400);
			return;
		}

		$flags = $this->getFeatureFlags();
		$eventCards = $this->getTripEventCards($userId, $tripId, $flags, $eventDate);
		$coordinates = $this->buildCoordinatesForEvents($eventCards);

		$this->respondJson([
			'success' => true,
			'coordinates' => $coordinates
		]);
	}

	public function markEventDone() {
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$this->respondJson(['success' => false, 'message' => 'Method not allowed.'], 405);
			return;
		}

		$userId = $this->getAuthenticatedUserId();
		if (!$userId) {
			$this->respondJson(['success' => false, 'message' => 'Please log in first.'], 401);
			return;
		}

		$input = json_decode(file_get_contents('php://input'), true);
		if (!is_array($input)) {
			$this->respondJson(['success' => false, 'message' => 'Invalid request payload.'], 400);
			return;
		}

		$tripId = (int)($input['tripId'] ?? 0);
		$eventId = (int)($input['eventId'] ?? 0);
		$target = strtolower(trim((string)($input['target'] ?? '')));

		if ($tripId <= 0 || $eventId <= 0 || !in_array($target, ['driver', 'guide'], true)) {
			$this->respondJson(['success' => false, 'message' => 'Invalid request parameters.'], 400);
			return;
		}

		$this->regUserModel->syncTripLifecycleForUser($userId, $tripId);

		$flags = $this->getFeatureFlags();
		$trip = $this->fetchTripForUser($userId, $tripId, $flags);

		if (!$trip) {
			$this->respondJson(['success' => false, 'message' => 'Trip not found.'], 404);
			return;
		}

		if (!$this->isTripOngoing($trip)) {
			$this->respondJson(['success' => false, 'message' => 'Trip is not ongoing.'], 409);
			return;
		}

		if (!empty($flags['hasStartPin']) && $this->shouldRequireStartPin($trip) && empty($trip['startPin'])) {
			$this->respondJson([
				'success' => false,
				'message' => 'Start PIN is required before traveller confirmations can be marked.'
			], 409);
			return;
		}

		if (!empty($flags['hasPinMatch']) && (int)($trip['pinMatch'] ?? 0) !== 1) {
			$this->respondJson([
				'success' => false,
				'message' => 'Driver has not matched the start PIN yet.'
			], 409);
			return;
		}

		$eventCards = $this->getTripEventCards($userId, $tripId, $flags, null, $eventId);
		$event = !empty($eventCards) ? $eventCards[0] : null;

		if (!$event) {
			$this->respondJson(['success' => false, 'message' => 'Event not found for this trip.'], 404);
			return;
		}

		if ($target === 'driver') {
			if (empty($flags['hasTravellerDriverFlag'])) {
				$this->respondJson([
					'success' => false,
					'message' => 'Traveller-to-driver completion flag is unavailable in this environment.'
				], 409);
				return;
			}

			if (empty($event['hasDriverAssignment'])) {
				$this->respondJson([
					'success' => false,
					'message' => 'No accepted driver assignment found for this event.'
				], 409);
				return;
			}

			if (!empty($flags['hasDriverDoneFlag']) && empty($event['dDone'])) {
				$this->respondJson([
					'success' => false,
					'message' => 'Driver must mark this event done first.'
				], 409);
				return;
			}

			$this->db->query('UPDATE trip_events
							  SET tDoneDriver = 1
							  WHERE eventId = :eventId AND tripId = :tripId AND userId = :userId
							  LIMIT 1');
			$this->db->bind(':eventId', $eventId);
			$this->db->bind(':tripId', $tripId);
			$this->db->bind(':userId', $userId);

			if ($this->db->execute()) {
				$this->respondJson([
					'success' => true,
					'message' => 'Traveller confirmation for driver has been saved.'
				]);
				return;
			}

			$this->respondJson(['success' => false, 'message' => 'Failed to save driver confirmation.'], 500);
			return;
		}

		if (empty($flags['hasTravellerGuideFlag'])) {
			$this->respondJson([
				'success' => false,
				'message' => 'Traveller-to-guide completion flag is unavailable in this environment.'
			], 409);
			return;
		}

		$eventType = strtolower((string)($event['eventType'] ?? ''));
		if ($eventType !== 'travelspot') {
			$this->respondJson([
				'success' => false,
				'message' => 'Guide completion is only available for travel spot events.'
			], 409);
			return;
		}

		if (empty($event['hasGuideAssignment'])) {
			$this->respondJson([
				'success' => false,
				'message' => 'No accepted guide assignment found for this travel spot event.'
			], 409);
			return;
		}

		if (!empty($flags['hasGuideDoneFlag']) && empty($event['gDone'])) {
			$this->respondJson([
				'success' => false,
				'message' => 'Guide must mark this travel spot event done first.'
			], 409);
			return;
		}

		$this->db->query('UPDATE trip_events
						  SET tDoneGuide = 1
						  WHERE eventId = :eventId AND tripId = :tripId AND userId = :userId
						  LIMIT 1');
		$this->db->bind(':eventId', $eventId);
		$this->db->bind(':tripId', $tripId);
		$this->db->bind(':userId', $userId);

		if ($this->db->execute()) {
			$this->respondJson([
				'success' => true,
				'message' => 'Traveller confirmation for guide has been saved.'
			]);
			return;
		}

		$this->respondJson(['success' => false, 'message' => 'Failed to save guide confirmation.'], 500);
	}

	private function getAuthenticatedUserId() {
		return (int)getSession('user_id');
	}

	private function respondJson($payload, $statusCode = 200) {
		http_response_code($statusCode);
		header('Content-Type: application/json');
		echo json_encode($payload);
	}

	private function getFeatureFlags() {
		if (is_array($this->featureFlagsCache)) {
			return $this->featureFlagsCache;
		}

		$this->featureFlagsCache = [
			'hasStartPin' => $this->hasColumn('created_trips', 'startPin'),
			'hasPinMatch' => $this->hasColumn('created_trips', 'pinMatch'),
			'hasDriverDoneFlag' => $this->hasColumn('trip_events', 'dDone'),
			'hasGuideDoneFlag' => $this->hasColumn('trip_events', 'gDone'),
			'hasTravellerDriverFlag' => $this->hasColumn('trip_events', 'tDoneDriver'),
			'hasTravellerGuideFlag' => $this->hasColumn('trip_events', 'tDoneGuide')
		];

		return $this->featureFlagsCache;
	}

	private function hasColumn($tableName, $columnName) {
		try {
			$this->db->query('SELECT COUNT(*) AS total
							  FROM information_schema.COLUMNS
							  WHERE TABLE_SCHEMA = :tableSchema
								AND TABLE_NAME = :tableName
								AND COLUMN_NAME = :columnName');
			$this->db->bind(':tableSchema', DB_NAME);
			$this->db->bind(':tableName', $tableName);
			$this->db->bind(':columnName', $columnName);
			$row = $this->db->single();

			return $row && (int)$row->total > 0;
		} catch (Exception $e) {
			error_log('TripMarker hasColumn failed for ' . $tableName . '.' . $columnName . ': ' . $e->getMessage());
			return false;
		}
	}

	private function fetchTripForUser($userId, $tripId, $flags) {
		$fields = [
			'tripId',
			'userId',
			'tripTitle',
			'description',
			'numberOfPeople',
			'startDate',
			'endDate',
			'status',
			'updatedAt'
		];

		if (!empty($flags['hasStartPin'])) {
			$fields[] = 'startPin';
		}

		if (!empty($flags['hasPinMatch'])) {
			$fields[] = 'pinMatch';
		}

		$query = 'SELECT ' . implode(', ', $fields) . '
				  FROM created_trips
				  WHERE tripId = :tripId AND userId = :userId
				  LIMIT 1';

		$this->db->query($query);
		$this->db->bind(':tripId', $tripId);
		$this->db->bind(':userId', $userId);

		$trip = $this->db->single();
		if (!$trip) {
			return null;
		}

		return (array)$trip;
	}

	private function isTripOngoing($trip) {
		$status = strtolower((string)($trip['status'] ?? ''));
		return $status === 'ongoing';
	}

	private function shouldRequireStartPin($trip) {
		if (!$this->isTripOngoing($trip)) {
			return false;
		}

		$startDate = $this->normalizeDate($trip['startDate'] ?? null);
		$endDate = $this->normalizeDate($trip['endDate'] ?? null);
		if (!$startDate || !$endDate) {
			return true;
		}

		$today = date('Y-m-d');
		return $today >= $startDate && $today <= $endDate;
	}

	private function generateAndStoreStartPin($userId, $tripId, $flags) {
		$pin = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

		$setParts = [
			'startPin = :startPin',
			'updatedAt = CURRENT_TIMESTAMP'
		];

		if (!empty($flags['hasPinMatch'])) {
			$setParts[] = 'pinMatch = 0';
		}

		$query = 'UPDATE created_trips
				  SET ' . implode(', ', $setParts) . '
				  WHERE tripId = :tripId AND userId = :userId
				  LIMIT 1';

		$this->db->query($query);
		$this->db->bind(':startPin', $pin);
		$this->db->bind(':tripId', $tripId);
		$this->db->bind(':userId', $userId);

		if ($this->db->execute()) {
			return $pin;
		}

		return null;
	}

	private function getTripEventCards($userId, $tripId, $flags, $eventDate = null, $eventId = null) {
		$rawEvents = $this->getTripEventsRaw($userId, $tripId, $flags, $eventDate, $eventId);
		if (empty($rawEvents)) {
			return [];
		}

		$hasDriverAssignment = $this->hasAcceptedDriverAssignment($userId, $tripId);
		$acceptedGuidesByEvent = $this->getAcceptedGuideAssignments($userId, $tripId);

		$cards = [];
		foreach ($rawEvents as $event) {
			$cards[] = $this->mapEventCard($event, $hasDriverAssignment, $acceptedGuidesByEvent);
		}

		return $cards;
	}

	private function getTripEventsRaw($userId, $tripId, $flags, $eventDate = null, $eventId = null) {
		$columns = [
			'te.eventId',
			'te.tripId',
			'te.userId',
			'te.eventDate',
			'te.startTime',
			'te.endTime',
			'te.eventType',
			'te.eventStatus',
			'te.travelSpotId',
			'te.locationName',
			'te.latitude',
			'te.longitude',
			'te.description',
			'ts.spotName AS travelSpotName',
			'ts.overview AS travelSpotOverview'
		];

		$columns[] = !empty($flags['hasDriverDoneFlag']) ? 'COALESCE(te.dDone, 0) AS dDone' : '0 AS dDone';
		$columns[] = !empty($flags['hasGuideDoneFlag']) ? 'COALESCE(te.gDone, 0) AS gDone' : '0 AS gDone';
		$columns[] = !empty($flags['hasTravellerDriverFlag']) ? 'COALESCE(te.tDoneDriver, 0) AS tDoneDriver' : '0 AS tDoneDriver';
		$columns[] = !empty($flags['hasTravellerGuideFlag']) ? 'COALESCE(te.tDoneGuide, 0) AS tDoneGuide' : '0 AS tDoneGuide';

		$query = 'SELECT ' . implode(', ', $columns) . '
				  FROM trip_events te
				  LEFT JOIN travel_spots ts ON te.travelSpotId = ts.spotId
				  WHERE te.tripId = :tripId
					AND te.userId = :userId';

		if ($eventDate !== null) {
			$query .= ' AND te.eventDate = :eventDate';
		}

		if ($eventId !== null) {
			$query .= ' AND te.eventId = :eventId';
		}

		$query .= "
				  ORDER BY te.eventDate ASC,
						   CASE te.eventStatus
							   WHEN 'start' THEN 1
							   WHEN 'intermediate' THEN 2
							   WHEN 'end' THEN 3
							   ELSE 4
						   END,
						   te.startTime ASC,
						   te.eventId ASC";

		$this->db->query($query);
		$this->db->bind(':tripId', $tripId);
		$this->db->bind(':userId', $userId);

		if ($eventDate !== null) {
			$this->db->bind(':eventDate', $eventDate);
		}

		if ($eventId !== null) {
			$this->db->bind(':eventId', $eventId);
		}

		return $this->db->resultSet();
	}

	private function hasAcceptedDriverAssignment($userId, $tripId) {
		try {
			$this->db->query("SELECT requestId
							  FROM traveller_side_d_requests
							  WHERE tripId = :tripId
								AND rqUserId = :userId
								AND requestStatus = 'accepted'
							  ORDER BY COALESCE(updatedAt, createdAt) DESC, requestId DESC
							  LIMIT 1");
			$this->db->bind(':tripId', $tripId);
			$this->db->bind(':userId', $userId);
			$row = $this->db->single();

			return !empty($row);
		} catch (Exception $e) {
			error_log('TripMarker hasAcceptedDriverAssignment failed for trip ' . $tripId . ': ' . $e->getMessage());
			return false;
		}
	}

	private function getAcceptedGuideAssignments($userId, $tripId) {
		try {
			$this->db->query("SELECT g.eventId, g.guideId, g.guideFullName
							  FROM traveller_side_g_requests g
							  INNER JOIN (
								  SELECT eventId, MAX(id) AS latestId
								  FROM traveller_side_g_requests
								  WHERE tripId = :tripId
									AND userId = :userId
								  GROUP BY eventId
							  ) latest ON latest.latestId = g.id
							  WHERE g.status = 'accepted'
								AND g.guideId IS NOT NULL");
			$this->db->bind(':tripId', $tripId);
			$this->db->bind(':userId', $userId);
			$rows = $this->db->resultSet();

			$map = [];
			foreach ($rows as $row) {
				$eventId = (int)($row->eventId ?? 0);
				if ($eventId <= 0) {
					continue;
				}

				$map[$eventId] = [
					'guideId' => (int)($row->guideId ?? 0),
					'guideFullName' => $row->guideFullName ?? ''
				];
			}

			return $map;
		} catch (Exception $e) {
			error_log('TripMarker getAcceptedGuideAssignments failed for trip ' . $tripId . ': ' . $e->getMessage());
			return [];
		}
	}

	private function mapEventCard($event, $hasDriverAssignment, $acceptedGuidesByEvent) {
		$event = (array)$event;

		$eventId = (int)($event['eventId'] ?? 0);
		$eventType = strtolower((string)($event['eventType'] ?? ''));
		$isTravelSpot = $eventType === 'travelspot';

		$guideAssignment = $acceptedGuidesByEvent[$eventId] ?? null;
		$hasGuideAssignment = $isTravelSpot && !empty($guideAssignment['guideId']);

		$eventTitle = 'Trip Event';
		if ($isTravelSpot && !empty($event['travelSpotName'])) {
			$eventTitle = $event['travelSpotName'];
		} elseif (!empty($event['locationName'])) {
			$eventTitle = $event['locationName'];
		}

		$mapName = !empty($event['locationName'])
			? $event['locationName']
			: (!empty($event['travelSpotName']) ? $event['travelSpotName'] : null);

		$description = trim((string)($event['description'] ?? ''));
		if ($description === '' && !empty($event['travelSpotOverview'])) {
			$description = (string)$event['travelSpotOverview'];
		}

		return [
			'eventId' => $eventId,
			'tripId' => (int)($event['tripId'] ?? 0),
			'eventDate' => $event['eventDate'] ?? null,
			'startTime' => $event['startTime'] ?? null,
			'endTime' => $event['endTime'] ?? null,
			'eventType' => $event['eventType'] ?? null,
			'eventStatus' => $event['eventStatus'] ?? null,
			'travelSpotId' => !empty($event['travelSpotId']) ? (int)$event['travelSpotId'] : null,
			'locationName' => $event['locationName'] ?? null,
			'latitude' => $event['latitude'] !== null ? (float)$event['latitude'] : null,
			'longitude' => $event['longitude'] !== null ? (float)$event['longitude'] : null,
			'description' => $description,
			'eventTitle' => $eventTitle,
			'mapName' => $mapName,
			'travelSpotName' => $event['travelSpotName'] ?? null,
			'travelSpotOverview' => $event['travelSpotOverview'] ?? null,
			'hasDriverAssignment' => (bool)$hasDriverAssignment,
			'hasGuideAssignment' => (bool)$hasGuideAssignment,
			'guideFullName' => $hasGuideAssignment ? ($guideAssignment['guideFullName'] ?? '') : '',
			'dDone' => (int)($event['dDone'] ?? 0),
			'gDone' => (int)($event['gDone'] ?? 0),
			'tDoneDriver' => (int)($event['tDoneDriver'] ?? 0),
			'tDoneGuide' => (int)($event['tDoneGuide'] ?? 0)
		];
	}

	private function buildCoordinatesForEvents($eventCards) {
		$coordinates = [];

		foreach ($eventCards as $event) {
			$eventType = strtolower((string)($event['eventType'] ?? ''));
			$eventId = (int)($event['eventId'] ?? 0);

			if ($eventType === 'location') {
				if (is_numeric($event['latitude']) && is_numeric($event['longitude'])) {
					$coordinates[] = [
						'eventId' => $eventId,
						'eventType' => 'location',
						'name' => $event['locationName'] ?? 'Location',
						'lat' => (float)$event['latitude'],
						'lng' => (float)$event['longitude']
					];
				}
				continue;
			}

			if ($eventType === 'travelspot' && !empty($event['travelSpotId'])) {
				$itineraryCoordinates = $this->getTravelSpotItineraryCoordinates((int)$event['travelSpotId']);

				foreach ($itineraryCoordinates as $point) {
					$coordinates[] = [
						'eventId' => $eventId,
						'eventType' => 'travelSpot',
						'name' => $point['name'],
						'lat' => $point['lat'],
						'lng' => $point['lng']
					];
				}
			}
		}

		return $coordinates;
	}

	private function getTravelSpotItineraryCoordinates($spotId) {
		if ($spotId <= 0) {
			return [];
		}

		try {
			$this->db->query('SELECT pointName, latitude, longitude
							  FROM travel_spots_itinerary
							  WHERE spotId = :spotId
							  ORDER BY pointOrder ASC');
			$this->db->bind(':spotId', $spotId);
			$points = $this->db->resultSet();

			$coordinates = [];
			foreach ($points as $point) {
				if (!is_numeric($point->latitude) || !is_numeric($point->longitude)) {
					continue;
				}

				$coordinates[] = [
					'name' => $point->pointName ?? 'Travel Spot Point',
					'lat' => (float)$point->latitude,
					'lng' => (float)$point->longitude
				];
			}

			return $coordinates;
		} catch (Exception $e) {
			error_log('TripMarker getTravelSpotItineraryCoordinates failed for spot ' . $spotId . ': ' . $e->getMessage());
			return [];
		}
	}

	private function normalizeDate($dateValue) {
		if (empty($dateValue)) {
			return null;
		}

		try {
			$date = new DateTime((string)$dateValue);
			return $date->format('Y-m-d');
		} catch (Exception $e) {
			return null;
		}
	}

	private function buildDatePills($startDate, $endDate) {
		$normalizedStart = $this->normalizeDate($startDate);
		$normalizedEnd = $this->normalizeDate($endDate);

		if (!$normalizedStart || !$normalizedEnd) {
			return [];
		}

		$start = new DateTime($normalizedStart);
		$end = new DateTime($normalizedEnd);
		if ($end < $start) {
			$end = clone $start;
		}

		$today = date('Y-m-d');
		$activeDate = ($today >= $normalizedStart && $today <= $normalizedEnd)
			? $today
			: $normalizedStart;

		$pills = [];
		$cursor = clone $start;
		while ($cursor <= $end) {
			$iso = $cursor->format('Y-m-d');
			$pills[] = [
				'iso' => $iso,
				'day' => strtoupper($cursor->format('D')),
				'date' => $cursor->format('j'),
				'month' => strtoupper($cursor->format('M')),
				'isActive' => $iso === $activeDate
			];

			$cursor->modify('+1 day');
		}

		return $pills;
	}
}

?>
