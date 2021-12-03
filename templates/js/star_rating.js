document.addEventListener("DOMContentLoaded", function() {
    const stars = document.getElementsByClassName('rating-star');

    function setRating(rating) {
        document.getElementById('comment_stars').value = parseInt(rating)+1;
        Array.prototype.filter.call(stars, function(star) {
            if(star.id <= rating)
                selectStar(star);
            else
                removeStar(star);
        });
    }

    function selectStar(star) {
        star.classList.remove('bi-star');
        star.classList.add('bi-star-fill', 'selected');
    }

    function removeStar(star) {
        star.classList.remove('bi-star-fill', 'selected');
        star.classList.add('bi-star');
    }

    Array.prototype.filter.call(stars, function(star) {
        star.addEventListener('click', function(e) {
            setRating(e.target.id);
        });

        star.addEventListener('mouseover', function(e) {
            var rating = e.target.id;
            array_stars = Array.from(stars);
            array_stars.filter(star => star.id <= rating).forEach(function(item) {
                item.classList.remove('bi-star');
                item.classList.add('bi-star-fill');
            })
            array_stars.filter(star => star.id > rating).forEach(function(item) {
                item.classList.remove('bi-star-fill');
                item.classList.add('bi-star');
            })
                
        })
        
        star.addEventListener('mouseleave', function(e) {
            array_stars = Array.from(stars);
            array_stars.filter(star => star.classList.contains('selected')).forEach(function(item) {
                selectStar(item);
            });
            array_stars.filter(star => !star.classList.contains('selected')).forEach(function(item) {
                removeStar(item);
            });
        })
    });
});