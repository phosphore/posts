<?php
final class Pager {
	
	private $start_page;
	private $total_pages;

	public function paging($total_pages,$curr_pg) {
		if((PAGES % 2) == 0) {
			if($total_pages > PAGES) { 
				if($curr_pg > (PAGES /2)) {
					if($total_pages - $curr_pg <= PAGES /2) {
						$this->start_page = $total_pages - (PAGES - 1);
						$this->total_pages = $total_pages; 
					} else {
						$this->start_page = ($curr_pg - (PAGES  / 2)) + 1; 
						$this->total_pages = $curr_pg + (PAGES  / 2); 
					}
				} else {
					$this->total_pages = PAGES;
					$this->start_page = 1; 
				}
			} else {
				$this->total_pages = $total_pages;
				$this->start_page = 1;
			}
		} else {
			if($total_pages > PAGES) {
				if($curr_pg > ceil(PAGES /2)) {
					if($total_pages - $curr_pg <= floor(PAGES /2)) { 
						$this->start_page = $total_pages - (PAGES - 1); 
						$this->total_pages = $total_pages;
					} else {
						$this->start_page = $curr_pg - floor(PAGES /2); 
						$this->total_pages = $curr_pg + floor(PAGES /2); 
					}
				} else {
					$this->total_pages = PAGES;
					$this->start_page = 1;
				}
			} else {
				$this->total_pages = $total_pages;
				$this->start_page = 1;
			}

		}
	}

	public function start_page() {
		return $this->start_page;
	}

	public function total_pages() {
		return $this->total_pages;
	}
	
}
?>
