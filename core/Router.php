<?php
	class Router {
		private $routs;
		public $request;
		public $baseUrl = "/";
		//public $mod = 0; //dynamic = 1 mod = 0

		public function __construct( $routs, $baseUrl ) {
			$this->setBaseUrl( $baseUrl );
			$this->setRouts( $routs );
			$this->setRequest( $_SERVER["REQUEST_URI"] );
			$this->execute();
		}

		/**
		 * @param string $baseUrl
		 */
		public function setBaseUrl( string $baseUrl ) {
			$this->baseUrl = $baseUrl;
		}

		/**
		 * @param array $request
		 */
		public function setRequest( $request ) {
			$request = str_replace($this->baseUrl, "", $request);
			$request       = $this->makeReq( $request );
			$this->request = $request;
		}

		private function getType( $req ) {
			preg_match_all( '/\{{(.+?)\}}/', $req, $matches );
			return $matches[1][0];
		}

		public function makeReq( string $uri ): array {
			$uri = explode( "/", $uri );
			array_shift( $uri );

			return $uri;
		}

		/**
		 * @param array $routs
		 */
		public function setRouts( array $routs ) {
			$result = [];
			foreach ( $routs as $rout ) {
				$rout["uri"] = $this->makeReq( $rout["uri"] );
				$result[] = $rout;
			}
			$this->routs = $result;
		}

		private function execute() {
			if ($handle = $this->is_permalink()) {
				$handle();
			}elseif($handle = $this->is_dynamiclink()) {
				$handle[0]($handle[1][0]);
			}else {
				$this->error("404", "Not found");
			}
		}
		private function error($code, $status) {
			header("HTTP/1.0 $code $status");
			header("HTTP/1.1 $code $status");
			header("Status: $code $status");
		}
		private function is_permalink() {
			foreach ($this->routs as $rout) {
				$result = [];
				if (count($rout["uri"]) == count($this->request)) {
					for ($i = 0; $i < count($this->request); $i++) {
						if ($this->request[$i] == $rout["uri"][$i]) {
							$result[] = true;
						}
					}
					if (count($this->request) == count($result)) {
						return $rout["handle"];
					}
				}
			}
		}
		private function is_dynamiclink() {
			foreach ($this->routs as $rout) {
				$result = [];
				$params = [];
				if (count($rout["uri"]) == count($this->request)) {
					for ($i = 0; $i < count($this->request); $i++) {
						if ($this->request[$i] == $rout["uri"][$i]) {
							$result[] = true;
						}elseif ( preg_match( '/\{{(.+?)\}}/', $rout["uri"][$i] ) ) {
							$type = $this->getType( $rout["uri"][$i] );
							if ( is_string( $this->request[$i] ) && $type == "temp" ) {
								$params[] = $this->request[$i];
								$result[] = true;
							} else {
								return false;
							}
						}
					}
					if (count($this->request) == count($result)) {
						return [$rout["handle"], $params];
					}
				}
			}
		}
	}
?>
