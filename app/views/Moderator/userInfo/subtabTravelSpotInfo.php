<div class="page-header">
	<div class="page-header-content">
		<div class="page-title-section">
			<h1 class="page-title">Travel Spot Information</h1>
			<p class="page-subtitle">Search by travel spot name or spot ID and view all guides available for that location</p>
		</div>
	</div>
</div>

<div class="verification-section spot-search-section" id="spot-search-section">
	<div class="section-header">
		<div class="section-header-content">
			<h2>
				<i class="fas fa-map-location-dot"></i>
				Find Travel Spot Guides
			</h2>
			<div class="section-controls">
				<div class="search-filter-section">
					<div class="search-box">
						<i class="fas fa-search"></i>
						<input type="text" id="travelSpotSearchInput" placeholder="Enter spot ID or spot name" class="search-input">
					</div>
					<button id="travelSpotSearchBtn" class="btn btn-primary spot-search-btn">
						<i class="fas fa-search"></i>
						Search
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="travelSpotInfoAlert" class="spot-alert" style="display:none;"></div>

<div id="travelSpotResultsContainer" class="verification-section spot-results-container" style="display:none;">
	<div class="section-header">
		<div class="section-header-content">
			<h2>
				<i class="fas fa-users"></i>
				Guides For Matching Travel Spot(s)
			</h2>
			<div class="result-count" id="travelSpotResultCount">0 guides found</div>
		</div>
	</div>

	<div id="travelSpotResultsList" class="spot-results-list">
		<p class="empty-results">Search to load guide information.</p>
	</div>
</div>
