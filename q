[1mdiff --git a/modules/addons/colocrossing/Event.php b/modules/addons/colocrossing/Event.php[m
[1mindex d52cc09..092975c 100644[m
[1m--- a/modules/addons/colocrossing/Event.php[m
[1m+++ b/modules/addons/colocrossing/Event.php[m
[36m@@ -78,7 +78,7 @@[m [mabstract class ColoCrossing_Event {[m
 	 		throw new Exception($command['message']);[m
 	 	}[m
 [m
[31m-	 	return $command['result'] == 'success';[m
[32m+[m	[41m [m	[32mreturn $command;[m
 	}[m
 [m
 	/**[m
[1mdiff --git a/modules/addons/colocrossing/events/TicketCreatedEvent.php b/modules/addons/colocrossing/events/TicketCreatedEvent.php[m
[1mindex c066ff6..00f7903 100644[m
[1m--- a/modules/addons/colocrossing/events/TicketCreatedEvent.php[m
[1m+++ b/modules/addons/colocrossing/events/TicketCreatedEvent.php[m
[36m@@ -52,33 +52,51 @@[m [mclass ColoCrossing_TicketCreatedEvent extends ColoCrossing_Event {[m
 			return false;[m
 		}[m
 [m
[31m-		$department_id = intval($department->getId());[m
[31m-[m
[32m+[m		[32m$subject = $this->getSubject();[m
 		$message = $this->getMessage();[m
 [m
[31m-		if(empty($message)) {[m
[32m+[m		[32mif(empty($subject) || empty($message)) {[m
 			return false;[m
 		}[m
 [m
[31m-		$subject = $this->ticket->getSubject();[m
 		$priority = $this->getPriority();[m
[32m+[m		[32m$status = $this->getStatus();[m
[32m+[m
[32m+[m		[32m$devices = $this->ticket->getDevices();[m
[32m+[m
[32m+[m		[32mforeach ($devices as $device) {[m
[32m+[m			[32m$service = ColoCrossing_Model_Service::findByDevice($device);[m
[32m+[m
[32m+[m			[32mif(empty($service)) {[m
[32m+[m				[32mcontinue;[m
[32m+[m			[32m}[m
 [m
[31m-		$clients = $this->getClients();[m
[32m+[m			[32m$client = $service->getClient();[m
 [m
[31m-		foreach ($clients as $client) {[m
[31m-			$client_id = intval($client->getId());[m
[32m+[m			[32mif(empty($service)) {[m
[32m+[m				[32mcontinue;[m
[32m+[m			[32m}[m
[32m+[m
[32m+[m			[32m$subject = $this->getSubject($device);[m
 [m
[31m-			$this->executeWHMCSCommand('openticket', array([m
[31m-				'clientid' => $client_id,[m
[31m-				'deptid' => $department_id,[m
[32m+[m			[32m$ticket = $this->executeWHMCSCommand('openticket', array([m
[32m+[m	[41m [m			[32m'subject' => $this->formatSubject($subject, $device, $service),[m
[32m+[m				[32m'message' => $this->formatMessage($message, $device, $service),[m
[32m+[m				[32m'clientid' => $client->getId(),[m
[32m+[m				[32m'deptid' => $department->getId(),[m
 				'admin' => true,[m
[31m-	 			'subject' => $subject,[m
[31m-				'message' => $message,[m
 	 			'priority' => $priority[m
 	 		));[m
[32m+[m
[32m+[m	[41m [m		[32mvar_dump($ticket);[m
[32m+[m
[32m+[m	[41m [m		[32m$this->executeWHMCSCommand('updateticket', array([m
[32m+[m	[41m [m			[32m'ticketid' => intval($ticket['id']),[m
[32m+[m	[41m [m			[32m'status' => $status[m
[32m+[m	[41m [m		[32m));[m
 		}[m
 [m
[31m- 		return count($clients) > 0;[m
[32m+[m[41m [m		[32mreturn true;[m
 	}[m
 [m
 	/**[m
[36m@@ -113,6 +131,14 @@[m [mclass ColoCrossing_TicketCreatedEvent extends ColoCrossing_Event {[m
 	}[m
 [m
 	/**[m
[32m+[m	[32m * Retrieves the Subject of the Ticket[m
[32m+[m	[32m * @return string The Subject[m
[32m+[m	[32m */[m
[32m+[m	[32mprivate function getSubject() {[m
[32m+[m		[32mreturn $this->ticket->getSubject();[m
[32m+[m	[32m}[m
[32m+[m
[32m+[m	[32m/**[m
 	 * Retrieves the Priority of the Ticket[m
 	 * @return string|null The Priority[m
 	 */[m
[36m@@ -157,27 +183,50 @@[m [mclass ColoCrossing_TicketCreatedEvent extends ColoCrossing_Event {[m
 	}[m
 [m
 	/**[m
[31m-	 * Get the Clients with Devices Assigned to Ticket[m
[32m+[m	[32m * Get the Status for the new Ticket[m
 	 *[m
[31m-	 * @return array<ColoCrossing_Model_Client> The Clients[m
[32m+[m	[32m * @return string The Status[m
 	 */[m
[31m-	public function getClients() {[m
[31m-		$devices = $this->ticket->getDevices();[m
[31m-		$clients = array();[m
[32m+[m	[32mpublic function getStatus() {[m
[32m+[m		[32mreturn 'On Hold';[m
[32m+[m	[32m}[m
 [m
[31m-		foreach ($devices as $device) {[m
[31m-			$service = ColoCrossing_Model_Service::findByDevice($device);[m
[32m+[m	[32m/**[m
[32m+[m	[32m * Formats the Subject for the new Ticket[m
[32m+[m	[32m * @param  string 						$subject The Subject[m
[32m+[m	[32m * @param  ColoCrossing_Object_Device 	$device  The Device[m
[32m+[m	[32m * @param  ColoCrogging_Model_Service 	$service The Service[m
[32m+[m	[32m * @return string         The Formatted Subject[m
[32m+[m	[32m */[m
[32m+[m	[32mpublic function formatSubject($subject, $device, $service) {[m
[32m+[m		[32m$service_hostname = $service->getHostname();[m
[32m+[m
[32m+[m		[32mif(empty($service_hostname)) {[m
[32m+[m			[32mreturn $subject;[m
[32m+[m		[32m}[m
 [m
[31m-			if(isset($service)) {[m
[31m-				$client = $service->getClient();[m
[32m+[m		[32m$device_name = $device->getName();[m
 [m
[31m-				if(isset($client)) {[m
[31m-					$clients[] = $client;[m
[31m-				}[m
[31m-			}[m
[32m+[m		[32mreturn str_replace($device_name, $service_hostname, $subject);[m
[32m+[m	[32m}[m
[32m+[m
[32m+[m	[32m/**[m
[32m+[m	[32m * Formats the Message for the new Ticket[m
[32m+[m	[32m * @param  string 						$message The Message[m
[32m+[m	[32m * @param  ColoCrossing_Object_Device 	$device  The Device[m
[32m+[m	[32m * @param  ColoCrogging_Model_Service 	$service The Service[m
[32m+[m	[32m * @return string         The Formatted Message[m
[32m+[m	[32m */[m
[32m+[m	[32mpublic function formatMessage($message, $device, $service) {[m
[32m+[m		[32m$service_hostname = $service->getHostname();[m
[32m+[m
[32m+[m		[32mif(empty($service_hostname)) {[m
[32m+[m			[32mreturn $message;[m
 		}[m
 [m
[31m-		return array_unique($clients);[m
[32m+[m		[32m$device_name = $device->getName();[m
[32m+[m
[32m+[m		[32mreturn str_replace($device_name, $device_name . ' (' . $service_hostname . ')', $message);[m
 	}[m
 [m
 }[m
\ No newline at end of file[m
