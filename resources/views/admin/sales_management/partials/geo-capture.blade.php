{{-- Driver location capture for trip actions (see App\Models\TripLocation).
     Forms:  <form data-geo-event="trip_created|route_plan|expense"> get geo_* hidden fields on submit.
     Links:  <a data-geo-link data-geo-url="POST url"> post the location first, then follow the link.
     Saving is never blocked: without a position, geo_status says why ("no location"). --}}
<script>
(function () {
    var CSRF = @json(csrf_token());

    function getPosition(done) {
        if (!navigator.geolocation) {
            return done({ status: 'unsupported' });
        }
        navigator.geolocation.getCurrentPosition(function (pos) {
            done({ status: 'ok', lat: pos.coords.latitude, lng: pos.coords.longitude, accuracy: Math.round(pos.coords.accuracy) });
        }, function (err) {
            done({ status: err.code === 1 ? 'denied' : (err.code === 3 ? 'timeout' : 'unavailable') });
        }, { enableHighAccuracy: true, timeout: 10000, maximumAge: 60000 });
    }

    // Saving continues either way; tell the driver how to fix it next time.
    function explain(status) {
        var why = {
            denied: 'You did not allow location. / Hukuruhusu mahali.',
            unavailable: 'The phone could not find your location. / Simu haikupata mahali ulipo.',
            timeout: 'Finding your location took too long. / Ilichukua muda mrefu kupata mahali.',
            unsupported: 'This browser cannot share location. / Kivinjari hiki hakiwezi kutoa mahali.'
        }[status];
        if (why) {
            alert(why + '\n\nSaved without location. Please turn ON Location (GPS) on the phone and allow location for this site (Chrome: lock icon > Permissions > Location).\n\nImehifadhiwa bila mahali. Tafadhali washa Location (GPS) kwenye simu na ruhusu mahali kwa tovuti hii.');
        }
    }

    function setField(form, name, value) {
        var input = form.querySelector('input[name="' + name + '"]');
        if (!input) {
            input = document.createElement('input');
            input.type = 'hidden';
            input.name = name;
            form.appendChild(input);
        }
        input.value = value === undefined || value === null ? '' : value;
    }

    function notice() {
        var el = document.createElement('div');
        el.className = 'small text-muted mb-2';
        el.innerHTML = '&#128205; Your location is recorded when you save (allow location when the phone asks). / Mahali ulipo panarekodiwa unapohifadhi.';
        return el;
    }

    function init() {
        document.querySelectorAll('form[data-geo-event]').forEach(function (form) {
            form.insertBefore(notice(), form.firstChild);

            form.addEventListener('submit', function (e) {
                if (form.dataset.geoDone) {
                    return;
                }
                e.preventDefault();
                form.querySelectorAll('[type=submit]').forEach(function (b) { b.disabled = true; });

                getPosition(function (geo) {
                    setField(form, 'geo_status', geo.status);
                    setField(form, 'geo_lat', geo.lat);
                    setField(form, 'geo_lng', geo.lng);
                    setField(form, 'geo_accuracy', geo.accuracy);
                    form.dataset.geoDone = '1';
                    explain(geo.status);
                    form.submit();
                });
            });
        });

        document.querySelectorAll('a[data-geo-link]').forEach(function (link) {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                link.classList.add('disabled');

                getPosition(function (geo) {
                    var body = new FormData();
                    body.append('_token', CSRF);
                    body.append('geo_status', geo.status);
                    if (geo.status === 'ok') {
                        body.append('geo_lat', geo.lat);
                        body.append('geo_lng', geo.lng);
                        body.append('geo_accuracy', geo.accuracy);
                    }
                    explain(geo.status);
                    var go = function () { window.location.href = link.href; };
                    fetch(link.dataset.geoUrl, { method: 'POST', body: body, credentials: 'same-origin' }).then(go, go);
                });
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
