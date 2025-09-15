<?php include 'header.php'; ?>
<style>
    body {
        background: #f5e8c7; /* Warm parchment background */
        font-family: 'Georgia', serif;
        color: #4a2c2a; /* Dark reddish-brown text */
    }
    .full-width-image {
        position: relative;
        width: 100%;
        height: 400px;
        background: url('all image/WhatsApp Image 2025-09-11 at 9.10.49 AM.jpeg') no-repeat center center;
        background-size: cover;
        margin-bottom: 40px;
    }
    .overlay-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: #fff;
        font-size: 3em;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        background: rgba(0, 0, 0, 0.5);
        padding: 15px 30px;
        border-radius: 10px;
        letter-spacing: 2px;
    }
    .card-container {
        padding: 20px;
        max-width: 1200px;
        margin: 0 auto;
    }
    .card {
        border: none;
        background: #fffaf0; /* Off-white with warm tone */
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
        border-radius: 15px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin-bottom: 30px;
    }
    .card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
    }
    .card-img-top {
        height: 250px;
        object-fit: cover;
        filter: sepia(30%) brightness(90%) contrast(110%);
        border-bottom: 3px solid #8b4513; /* Dark brown border */
    }
    .card-body {
        padding: 20px;
        background: #f8f1e9; /* Warm beige */
    }
    .card-title {
        font-size: 1.8em;
        color: #4a2c2a;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        border-bottom: 1px dashed #8b4513;
        padding-bottom: 10px;
    }
    .card-text {
        color: #6b4e31; /* Muted brown */
        font-size: 1.1em;
        line-height: 1.6;
    }
    .btn-primary {
        background-color: #8b4513; /* Saddle brown */
        border: none;
        padding: 10px 25px;
        font-size: 1.1em;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-radius: 5px;
    }
    .btn-primary:hover {
        background-color: #6b4e31; /* Darker shade */
        color: #fff;
    }
    @media (max-width: 768px) {
        .card-img-top {
            height: 200px;
        }
        .card-title {
            font-size: 1.5em;
        }
        .overlay-text {
            font-size: 2em;
        }
    }
</style>

<div class="full-width-image">
    <div class="overlay-text">Discover Sri Lanka's Heritage - 05:47 PM, Sep 14, 2025</div>
</div>

<div class="card-container">
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="all image/tourist_guide.jpg" class="card-img-top" alt="Tourist Guide">
                <div class="card-body">
                    <h5 class="card-title">Tourist Guide</h5>
                    <p class="card-text">Uncover essential tips and hidden spots for an unforgettable journey.</p>
                    <a href="tourist_guide.php" class="btn btn-primary">Explore</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="https://via.placeholder.com/400x250?text=Supplier+Services" class="card-img-top" alt="Supplier & Services">
                <div class="card-body">
                    <h5 class="card-title">Supplier & Services</h5>
                    <p class="card-text">Connect with trusted suppliers for your travel needs.</p>
                    <a href="supplier_services.php" class="btn btn-primary">Explore</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="https://via.placeholder.com/400x250?text=Cultural+Exchange" class="card-img-top" alt="Cultural Exchange">
                <div class="card-body">
                    <h5 class="card-title">Cultural Exchange</h5>
                    <p class="card-text">Engage with locals for authentic cultural experiences.</p>
                    <a href="cultural_exchange.php" class="btn btn-primary">Explore</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="https://via.placeholder.com/400x250?text=Festivals+Celebrations" class="card-img-top" alt="Festivals & Celebrations">
                <div class="card-body">
                    <h5 class="card-title">Festivals & Celebrations</h5>
                    <p class="card-text">Immerse in the vibrant traditions of Sri Lanka.</p>
                    <a href="festivals_celebrations.php" class="btn btn-primary">Explore</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="https://via.placeholder.com/400x250?text=Arts+Crafts" class="card-img-top" alt="Arts & Crafts">
                <div class="card-body">
                    <h5 class="card-title">Arts & Crafts</h5>
                    <p class="card-text">Admire the beauty of handmade Sri Lankan artistry.</p>
                    <a href="arts_crafts.php" class="btn btn-primary">Explore</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="https://via.placeholder.com/400x250?text=Remembrance+Places" class="card-img-top" alt="Remembrance Places">
                <div class="card-body">
                    <h5 class="card-title">Remembrance Places</h5>
                    <p class="card-text">Pay homage at historic sites of significance.</p>
                    <a href="remembrance_places.php" class="btn btn-primary">Explore</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="https://via.placeholder.com/400x250?text=Dance+Music" class="card-img-top" alt="Dance & Traditional Music">
                <div class="card-body">
                    <h5 class="card-title">Dance & Traditional Music</h5>
                    <p class="card-text">Enjoy the rhythms of Sri Lankan heritage.</p>
                    <a href="dance_traditional_music.php" class="btn btn-primary">Explore</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <img src="all image/grilled-pork-with-rice-guacamole-cilantro-generated-by-ai.jpg" class="card-img-top" alt="Food & Culinary Culture">
                <div class="card-body">
                    <h5 class="card-title">Food & Culinary Cultures</h5>
                    <p class="card-text">Taste the rich flavors of Sri Lankan cuisine.</p>
                    <a href="food_culinary_culture.php" class="btn btn-primary">Explore</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>