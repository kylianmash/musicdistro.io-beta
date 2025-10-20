<?php
return array_replace_recursive(
    include __DIR__ . '/en.php',
    [
        'language' => [
            'label' => 'Kieli',
            'menu_label' => 'Vaihda kieltä',
            'choose_language' => 'Valitse kieli',
            'close_menu' => 'Sulje kielivalikko',
            'updated' => 'Kieliasetuksesi on päivitetty (:language).',
        ],
        'alerts' => [
            'blocked_access' => 'Pääsysi on estetty.',
        ],
        'email' => [
            'common' => [
                'greeting' => 'Hei :name,',
                'greeting_generic' => 'Hei,',
                'signature' => 'Nähdään pian,\n:site-tiimi',
            ],
            'verification' => [
                'subject' => 'Vahvista sähköpostisi palvelussa :site',
                'intro' => 'Tervetuloa palveluun :site! Aktivoi tilisi ja aloita musiikkisi jakelu käyttämällä alla olevaa linkkiä.',
                'action' => 'Vahvistuslinkki: :link',
                'footer' => 'Jos et pyytänyt tätä rekisteröintiä, voit jättää tämän viestin huomiotta.',
            ],
            'reset' => [
                'subject' => 'Palauta :site-salasanasi',
                'intro' => 'Pyysit salasanasi palauttamista palvelussa :site.',
                'action' => 'Valitse uusi salasana avaamalla seuraava linkki: :link',
                'expiration' => 'Tämä linkki vanhenee 60 minuutissa. Jos et pyytänyt palautusta, voit turvallisesti jättää tämän viestin huomiotta.',
            ],
        ],
        'auth' => [
            'roles' => [
                'musician' => 'Muusikko',
                'artist' => 'Artisti',
                'manager' => 'Manageri',
                'producer' => 'Tuottaja',
                'publisher' => 'Julkaisija',
                'label' => 'Levy-yhtiö',
                'other' => 'Muu',
                'member' => 'Jäsen',
            ],
            'common' => [
                'first_name_label' => 'Etunimi',
                'last_name_label' => 'Sukunimi',
                'email_label' => 'Sähköpostiosoite',
                'country_label' => 'Asuinmaa',
                'role_label' => 'Profiilisi',
                'language_label' => 'Ensisijainen kieli',
                'password_label' => 'Salasana',
                'confirm_password_label' => 'Vahvista salasana',
            ],
            'login' => [
                'title' => 'Kirjaudu sisään',
                'lead' => 'Palaa hallintapaneeliin ja jatka legendasi rakentamista.',
                'submit' => 'Kirjaudu sisään',
                'forgot' => 'Unohtuiko salasana?',
                'register_prompt' => 'Ei vielä tiliä? :link.',
                'register_link' => 'Luo tili',
            ],
            'register' => [
                'intro_title' => 'Liity palveluun :site ja anna tekoälyn vahvistaa ääntäsi.',
                'intro_text' => 'Yhdistämme jokaisen artistin yleisöön, joka jo odottaa heidän musiikkiaan. Rekisteröidy julkaistaksesi ensimmäisen julkaisusi muutamassa minuutissa.',
                'bullets' => [
                    'native_ai' => 'Tekoälypohjainen markkinointi kampanjoihin, soittolistapitchauksiin ja faninäkemyksiin.',
                    'worldwide' => 'Välitön maailmanlaajuinen jakelu yli 250 premium-alustalla.',
                    'royalties' => 'Pidä 100 % rojalteistasi läpinäkyvällä seurannalla ja reaaliaikaisilla ilmoituksilla.',
                ],
                'title' => 'Luo tilisi',
                'lead' => 'Jaa tietosi saadaksesi vahvistuslinkin.',
                'language_help' => 'Mukautamme hallintapaneelin, sähköpostit ja aloituksen tälle kielelle.',
                'submit' => 'Aktivoi tilini',
                'login_prompt' => 'Oletko jo jäsen? :link.',
                'login_link' => 'Kirjaudu sisään',
                'success' => 'Kiitos! Tarkista sähköpostisi vahvistaaksesi osoitteesi ja aktivoidaksesi tilisi.',
            ],
            'forgot' => [
                'title' => 'Unohditko salasanasi?',
                'lead' => 'Anna sähköpostiosoitteesi saadaksesi ohjeet salasanan palautukseen.',
                'submit' => 'Lähetä palautuslinkki',
                'back_to_login' => 'Takaisin kirjautumiseen',
                'success' => 'Jos tili vastaa tätä sähköpostiosoitetta, lähetimme juuri ohjeet salasanan palauttamiseen.',
            ],
            'reset' => [
                'title' => 'Valitse uusi salasana',
                'lead' => 'Valitse vahva salasana turvataksesi tilisi uudelleen.',
                'submit' => 'Päivitä salasana',
                'token_invalid' => 'Tämä palautuslinkki on virheellinen tai puutteellinen. Pyydä uusi linkki.',
                'token_expired' => 'Tämä palautuslinkki on vanhentunut. Pyydä uusi linkki.',
                'token_used' => 'Tätä palautuslinkkiä ei voi enää käyttää. Pyydä uusi linkki.',
                'success' => 'Salasanasi on päivitetty. Voit nyt kirjautua sisään.',
                'new_password_label' => 'Uusi salasana',
                'confirm_password_label' => 'Vahvista salasana',
                'request_new_link' => 'Pyydä uusi linkki',
                'return_to_login' => 'Palaa kirjautumiseen',
            ],
            'verify' => [
                'expired_title' => 'Vanhentunut tai virheellinen linkki',
                'expired_body' => 'Käyttämäsi vahvistuslinkki ei ole enää voimassa. Ota yhteyttä tukeen osoitteessa :email saadaksesi uuden.',
                'cta_login' => 'Takaisin kirjautumiseen',
                'success' => 'Sähköpostiosoitteesi on vahvistettu. Voit nyt kirjautua sisään.',
            ],
            'blocked' => [
                'title' => 'Pääsy rajoitettu',
                'lead' => 'Tarvitsetko apua? Kirjoita osoitteeseen :email niin tiimimme auttaa sinua nopeasti.',
                'cta_login' => 'Takaisin kirjautumiseen',
            ],
            'profile' => [
                'updated' => 'Muutokset tallennettu.',
            ],
        ],
        'dashboard' => [
            'title' => 'Hallintapaneeli – :site',
            'brand_alt' => ':site-hallintapaneeli',
            'profile_panel' => [
                'title' => 'Profiilisi',
                'helper' => 'Päivitä henkilötietosi, maa, kieli ja profiilikuva pitääksesi läsnäolosi yhtenäisenä.',
                'remove_photo' => 'Poista kuva',
                'remove_photo_sr' => 'Poista kuva',
                'change_photo' => 'Vaihda kuva',
                'preview_alt' => 'Profiilikuvan esikatselu',
                'photo_alt' => 'Profiilikuva',
                'close' => 'Sulje paneeli',
                'labels' => [
                    'first_name' => 'Etunimi',
                    'last_name' => 'Sukunimi',
                    'country' => 'Maa',
                    'role' => 'Ammatillinen profiili',
                    'language' => 'Kieli',
                    'address_line1' => 'Osoiterivi 1',
                    'address_line2' => 'Osoiterivi 2',
                    'postal_code' => 'Postinumero',
                    'city' => 'Kaupunki',
                    'phone_number' => 'Puhelinnumero',
                    'business_type' => 'Tilin tyyppi',
                    'company_name' => 'Yrityksen nimi',
                    'company_vat' => 'ALV-/verotunnus',
                ],
                'language_help' => 'Mukautamme hallintapaneelin ja ilmoitukset tälle kielelle.',
                'business_type_helper' => 'Valitse toimitko yksityishenkilönä vai yrityksen puolesta.',
                'business_fields_helper' => 'Yrityksen tiedot näkyvät laskuissa ja hallinnollisissa viennissä.',
                'business_type_options' => [
                    'individual' => 'Yksityishenkilö',
                    'company' => 'Yritys',
                ],
                'submit' => 'Tallenna muutokset',
                'submit_processing' => 'Tallennetaan…',
                'logout' => 'Kirjaudu ulos',
                'feedback' => [
                    'saving' => 'Tallennetaan…',
                    'image_optimizing' => 'Optimoidaan kuvaasi…',
                    'image_ready' => 'Kuva valmis tallennettavaksi.',
                    'image_error' => 'Kuvan käsittelyssä tapahtui virhe.',
                    'photo_removed' => 'Kuva poistettu. Muista tallentaa.',
                    'profile_refresh' => 'Profiilisi on päivitetty. Päivitetään näkymää…',
                    'profile_success' => 'Profiilisi päivitettiin onnistuneesti.',
                    'profile_error' => 'Profiilin päivitys epäonnistui.',
                    'unexpected_error' => 'Tapahtui odottamaton virhe. Yritä uudelleen.',
                ],
            ],
            'welcome' => [
                'title' => 'Hei :name, valmis ravistelemaan maailmaa?',
                'body' => 'Työtilasi kokoaa yhteen kaikki työkalut, joilla muutat kappaleesi viraaleiksi kokemuksiksi. Valmistele julkaisut, orkestroi jakelusi ja seuraa jokaisen fanin reaktiot reaaliajassa.',
            ],
            'studio_card' => [
                'aria_label' => 'Käynnistä MusicDistro Studio',
                'badge' => 'UUTUUS',
                'title' => 'Sävelä, miksaa ja masteroi selaimessa',
                'subtitle' => 'Avaa ammattitason Music Studio aikajanoineen, miksereineen, plugineineen ja reaaliaikaisine vientiominaisuuksineen.',
                'cta' => 'Avaa Music Studio',
            ],
            'cards' => [
                'distribution' => [
                    'title' => 'Musiikin jakelu',
                    'description' => 'Käynnistä uudet julkaisut, kokoa albumit ja seuraa rojalteja yhdistetystä jakelukonsolista. Koko toimitusketjusi on yhden klikkauksen päässä.',
                    'link_label' => 'Avaa jakelukonsoli',
                    'type' => 'modal',
                    'modal_target' => 'musicDistributionModal',
                    'service_url' => '/generate-token/',
                ],
                'tutorial' => [
                    'title' => 'Jakelun vaiheittainen opas',
                    'description' => 'Seuraa visuaalista opastamme masterien valmisteluun, metadatan määrittelyyn ja vaikuttavaan lanseeraukseen.',
                    'link_label' => 'Näytä opas',
                    'href' => '/tutorial.php',
                ],
                'royalties' => [
                    'title' => 'Kerää 100 % rojalteistasi',
                    'description' => 'Avaa premium-maksut Spotifyssa, Apple Musicissa, YouTubessa, Amazon Musicissa ja yli 150 DSP:ssä yhdellä päivityksellä.',
                    'link_label' => 'Tutustu premium-suunnitelmaan',
                    'type' => 'modal',
                    'modal_target' => 'royaltiesModal',
                    'variant' => 'highlight',
                ],
                'mastering' => [
                    'title' => 'Sisäinen tekoälymasterointi',
                    'description' => 'Toimita radiovalmiit masterit muutamassa minuutissa. Pudota miksauksesi, kokeile pro-esiasetuksia ja vie tulokset heti.',
                    'link_label' => 'Avaa masterointistudio',
                    'type' => 'modal',
                    'modal_target' => 'masteringModal',
                    'variant' => 'highlight',
                ],
                'smartlinks' => [
                    'title' => 'Konvertoivat smartlinkit',
                    'description' => 'Luo laskeutumissivuja, jotka ohjaavat fanit Spotifyyn, Apple Musiciin, Deezeriin, Amazon Musiciin, YouTube Musiciin ja kaikkiin yhdistettyihin DSP:ihin sekunneissa.',
                    'link_label' => 'Hallinnoi smartlinkkejä',
                    'type' => 'modal',
                    'modal_target' => 'smartlinksModal',
                    'variant' => 'highlight',
                ],
                'ai_composer' =>
      array (
        'title' => 'AI music composer',
        'description' => 'Draft lyrics, audition Suno-inspired vocalists and render AI instrumentals from your creative brief.',
        'link_label' => 'Launch the AI composer',
        'type' => 'modal',
        'modal_target' => 'aiComposerModal',
        'variant' => 'highlight',
      ),
      'cloud_storage' => [
                    'title' => 'Musicdistro Cloud artisteille',
                    'description' => 'Säilytä, aikaleimaa ja jaa masterit salatussa holvissa, jonka omistajuus todistetaan lohkoketjulla.',
                    'link_label' => 'Tutustu Musicdistro Cloudiin',
                    'type' => 'modal',
                    'modal_target' => 'cloudStorageModal',
                    'variant' => 'highlight',
                    'service_url' => '/cloud-storage',
                ],
                'youtube' => [
                    'title' => 'YouTube-oikeuksien hallinta',
                    'description' => 'Synkronoi YouTube Content ID ja yhdistä viralliset artistikanavasi keskittääksesi katalogisi.',
                    'link_label' => 'Määritä YouTube',
                    'type' => 'modal',
                    'modal_target' => 'youtubeModal',
                    'variant' => 'highlight',
                ],
                'publishing_administration' => [
                    'title' => 'Julkaisuhallinta',
                    'description' => 'Rekisteröi sävellykset, kerää lähioikeuskorvaukset ja seuraa globaalit rojalti-jaot reaaliajassa.',
                    'link_label' => 'Hallinnoi julkaisua',
                    'type' => 'modal',
                    'modal_target' => 'publishingAdministrationModal',
                ],
                'royalties_withdrawal' => [
                    'title' => 'Rojaltien kotiutus',
                    'description' => 'Käynnistä maksut, valitse pankkitilit ja seuraa kotiutuksia kaikista katalogeista yhdessä näkymässä.',
                    'link_label' => 'Kotiuta rojalteja',
                    'type' => 'modal',
                    'modal_target' => 'royaltiesWithdrawalModal',
                ],
                'coaching' => [
                    'title' => 'Tekoälyvalmennus ja kampanjat',
                    'description' => 'Aktivoi tekoälyllä tehostetut kampanjat, tunnista sopivat soittolistat ja saat henkilökohtaisen promootiosuunnitelman.',
                    'link_label' => 'Tulossa pian',
                    'alert' => 'Tekoälykampanjat ovat pian saatavilla.',
                ],
                'payments' => [
                    'title' => 'Laskutus ja maksut',
                    'description' => 'Tarkastele jokaista Stripe-maksua, lataa laskut ja seuraa tulevia uusintoja.',
                    'link_label' => 'Näytä maksuhistoria',
                    'type' => 'modal',
                    'modal_target' => 'paymentsModal',
                ],
            ],
            'musicdistribution_modal' => [
                'badge' => 'Uusi',
                'title' => 'MusicDistribution-konsoli',
                'subtitle' => 'Koordinoi jokainen julkaisu omistetussa MusicDistribution-työtilassa. Käynnistä projekteja, lähetä toimituksia ja seuraa rojalteja poistumatta hallintapaneelista.',
                'features_title' => 'Mitä sisällä voit tehdä',
                'features' => [
                    'Luo singlejä, EP:itä ja albumeita ohjatuilla metadata-virroilla.',
                    'Lähetä julkaisut yli 250 DSP:hen ja seuraa ingestointia reaaliajassa.',
                    'Yhdistä tilitykset, seuraa ennakkoja ja vie rojalti-raportit välittömästi.',
                ],
                'cta_label' => 'Käynnistä MusicDistribution',
                'cta_processing' => 'Yhdistetään MusicDistributioniin…',
                'cta_error' => 'Ponnahdusikkunat on estetty. Salli ponnahdusikkunat ja yritä uudelleen.',
                'opt_out_label' => 'Älä näytä tätä esittelyä uudelleen',
                'disclaimer' => 'Avaamme MusicDistributionin uuteen välilehteen turvallisella kertakirjautumisella.',
                'actions' => [
                    'close' => 'Sulje',
                ],
                'cta_href' => '/generate-token/',
            ],
        'notifications' => [
                'panel' => [
                    'title' => 'Ilmoitukset',
                    'empty' => 'Olet ajan tasalla.',
                    'toggle' => 'Avaa ilmoitukset',
                    'close' => 'Sulje ilmoituspaneeli',
                ],
                'actions' => [
                    'open_profile' => 'Täydennä profiilini',
                    'open_link' => 'Avaa linkki',
                ],
                'items' => [
                    'profile_incomplete' => [
                        'title' => 'Täydennä profiilitietosi',
                        'message' => 'Lisää osoite, maa ja puhelinnumero, jotta saamme rekisteröintisi valmiiksi.',
                    ],
                ],
            ],
            'cloud_modal' => [
                'badge' => 'Uusi',
                'title' => 'Musicdistro Cloud artisteille',
                'subtitle' => 'Pidä jokainen miksaus, sopimus ja stemma yhdessä turvallisessa työtilassa, jonka etusija todistetaan lohkoketjulla.',
                'features_title' => 'Miksi artistit rakastavat cloudiamme',
                'features' => [
                    [
                        'title' => 'Omistajuus todennetaan lohkoketjulla',
                        'description' => 'Jokainen lataus sinetöidään aikaleimatulla hashilla, jotta voit todistaa tekijyyden heti.',
                    ],
                    [
                        'title' => 'Kestävä luova holvi',
                        'description' => 'Säilytä masterit, stemmat, kansitaide ja sopimukset salatussa, älykkäästi jäsennellyssä varmuuskopiossa.',
                    ],
                    [
                        'title' => 'Yhteistyötä hallitusti',
                        'description' => 'Jaa suojattuja linkkejä tarkoin oikeuksin, peruuta pääsy yhdellä klikkauksella ja seuraa käyttöä.',
                    ],
                ],
                'highlights_title' => 'Saat käyttöösi',
                'highlights' => [
                    'Versioseuranta ja yksityiskohtainen historia jokaiselle tiedostolle.',
                    'Reaaliaikaiset ilmoitukset aina, kun tiedostoja katsotaan tai ladataan.',
                    'Selain- ja mobiilikäyttö, jotta katalogisi kulkee mukanasi.',
                ],
                'pricing' => [
                    'title' => 'Läpinäkyvä hinnoittelu',
                    'usage' => [
                        'label' => 'Käytön mukaan laskutus',
                        'value' => ':price_mb / MB tallennustilaa + :price_file / lataus',
                    ],
                    'subscription' => [
                        'label' => 'Tilaukseen perustuva paketti',
                        'value' => ':price kuukaudessa sisältäen :storage :unit',
                        'storage_unit' => 'MB',
                    ],
                ],
                'cta_label' => 'Siirry palveluun',
                'cta_href' => '/cloud-storage',
                'opt_out_label' => 'Älä näytä tätä esittelyä uudelleen',
                'disclaimer' => 'Voit avata Musicdistro Cloudin milloin tahansa hallintapaneelista.',
                'actions' => [
                    'close' => 'Sulje',
                ],
            ],
            'royalties_modal' => [
                'headline' => 'Päivitä pitääksesi 100 % rojalteistasi',
                'subheadline' => 'Lopeta 30 % tuotoista luovuttaminen. Tehosta jokaista julkaisua premium-maksuilla, concierge-tuella ja laser-tarkoilla analytiikoilla.',
                'switch' => [
                    'label' => 'Valitse laskutustaajuus',
                    'monthly' => 'Kuukausittain',
                    'yearly' => 'Vuosittain',
                ],
                'plans' => [
                    'monthly' => [
                        'label' => 'Kuukausittain',
                        'tagline' => 'Täysi joustavuus – peru milloin tahansa.',
                        'price_main' => '9',
                        'price_decimal' => '.99',
                        'frequency' => '/kk',
                        'note' => 'Jatkuva jäsenyys. Voit perua milloin vain.',
                        'cta' => 'Valitse kuukausipaketti',
                    ],
                    'yearly' => [
                        'label' => 'Vuosittain',
                        'tagline' => 'Paras arvo – saat 2 kuukautta veloituksetta.',
                        'price_main' => '99',
                        'price_decimal' => '',
                        'frequency' => '/vuosi',
                        'note' => 'Optimoitu tavoitteellisille julkaisuille – säästä kahden kuukauden hinta.',
                        'cta' => 'Valitse vuosipaketti',
                    ],
                ],
                'features_title' => 'Kaikki mitä tarvitset ammattilaisuuteen',
                'features' => [
                    'Pidä 100 % rojalteistasi Spotifyssa, Apple Musicissa, YouTubessa, Amazon Musicissa, Deezerissä ja 150+ DSP:ssä.',
                    'Automaattiset laskut, ALV-valmiit raportit ja Stripen turva jokaisessa uusinnassa.',
                    'Priorisoitu jakelukaista, julkaisujen terveyden seuranta ja ennakoivat tulohälytykset.',
                    'Reaaliaikaiset analyysit suoratoistoistasi ja tuloistasi.',
                ],
                'plan_highlights_title' => 'Käytännön tiedot',
                'plan_highlights' => [
                    'Välitön aktivointi, kun maksu vahvistuu.',
                    'Peru milloin tahansa suoraan hallintapaneelista.',
                    'Laskut lähetetään sinulle automaattisesti sähköpostilla.',
                ],
                'guarantee' => 'Turvallinen Stripe-laskutus. Läpinäkyvä hinnoittelu, ei piilokuluja.',
                'actions' => [
                    'close' => 'Sulje',
                    'processing' => 'Yhdistetään Stripeen…',
                ],
                'checkout' => [
                    'generic_error' => 'Stripe-maksun käynnistäminen epäonnistui. Yritä uudelleen tai ota yhteyttä tukeen.',
                    'missing_key' => 'Stripen asetukset puuttuvat. Ota yhteyttä ylläpitäjään.',
                    'success_redirect' => 'Ohjataan turvalliseen maksuun…',
                ],
            ],
            'mastering_modal' => [
                'title' => 'Musicdistro Mastering Studio',
                'description' => 'Hyödynnä sisäistä tekoälymasterointia saadaksesi voimakkaammat ja kirkkaammat masterit poistumatta hallintapaneelista.',
                'dropzone' => [
                    'title' => 'Pudota kappaleesi',
                    'subtitle' => 'WAV, AIFF tai MP3 enintään 250 MB.',
                    'button' => 'Lataa tiedosto',
                    'hint' => 'Tai klikkaa selataksesi tietokoneesi tiedostoja.',
                ],
                'analysis' => [
                    'title' => 'Tekoälymasteroinnin konsoli',
                ],
                'status' => [
                    'idle' => 'Vedä ja pudota miksauksesi tai selaa tiedosto.',
                    'uploading' => 'Ladataan ääntä…',
                    'analyzing' => 'Analysoidaan transientteja ja sointia…',
                    'rendering' => 'Renderöidään lopullista masteria…',
                    'ready' => 'Master valmis – tutustu esiasetuksiin alta.',
                    'error' => 'Jotain meni vikaan. Yritä uudelleen toisella tiedostolla.',
                ],
                'processing' => [
                    'uploading' => 'Lähetetään masterointikonsoliin…',
                    'analyzing' => 'Kartoitetaan dynamiikkaa ja stereokuvaa…',
                    'rendering' => 'Viimeistellään masteriasi…',
                ],
                'player' => [
                    'title' => 'Esikuuntele ja vertaa',
                    'subtitle' => 'Valitse esiasetus ja paina play kuullaksesi parannuksen.',
                    'listen_master' => 'Kuuntele masteroitu versio',
                    'listen_original' => 'Vaihda alkuperäiseen miksaukseen',
                    'duration_placeholder' => '—:—',
                ],
                'presets' => [
                    'legend' => 'Jokainen ketju on masterointitiimimme virittämä eri julkaisuskenaarioihin.',
                    'categories' => [
                        'reference' => 'Referenssi',
                        'impact' => 'Impact',
                        'bounce' => 'Bounce',
                        'spark' => 'Spark',
                        'energy' => 'Energy',
                        'groove' => 'Groove',
                        'horizon' => 'Stage',
                        'analog' => 'Analog',
                        'air' => 'Air',
                        'manual' => 'Mukautettu',
                    ],
                    'original' => 'Alkuperäinen miksaus',
                    'radio' => 'Radiohitti',
                    'hiphop' => 'Hip-hop-voima',
                    'electro' => 'Electro Spark',
                    'edm' => 'EDM Max',
                    'dance' => 'Dancefloor Glow',
                    'festival' => 'Festival Impact',
                    'warm' => 'Warm Tape',
                    'spatial' => 'Spatial Air',
                    'custom' => 'Custom Sculpt',
                ],
                'actions' => [
                    'new_file' => 'Masteroi toinen kappale',
                ],
                'visualizer' => [
                    'title' => 'Reaaliaikainen AI-visualisointi',
                    'subtitle' => 'Seuraa masteriasi elävällä aaltomuodolla ja spektrienergian analytiikalla.',
                    'waveform' => 'Neuraalioskilloskooppi',
                    'spectrum' => 'Harmoninen energia',
                ],
                'controls' => [
                    'title' => 'Edistyneet säätimet',
                    'subtitle' => 'Muovaa masteriasi kuten moderni VST-insinööri: hienosäädä dynamiikkaa, sointia ja stereokuvaa reaaliajassa.',
                    'pre_gain' => 'Sisääntulovahvistus',
                    'threshold' => 'Kompura -kynnys',
                    'ratio' => 'Kompura -suhde',
                    'attack' => 'Attack',
                    'release' => 'Release',
                    'low' => 'Low enhancer',
                    'mid' => 'Mid sculpt',
                    'high' => 'Air boost',
                    'width' => 'Stereoleveys',
                    'output' => 'Ulostulon voimakkuus',
                    'mono' => 'Mono-kuuntelu',
                ],
                'errors' => [
                    'invalid_type' => 'Lataa WAV-, AIFF- tai MP3-tiedosto.',
                    'too_large' => 'Tiedosto on liian suuri. Maksimikoko on 250 MB.',
                    'load' => 'Äänitiedostoa ei voitu lukea. Yritä viedä se uudelleen.',
                ],
                'checkout' => [
                    'title' => 'Vie masterisi',
                    'hint' => 'Turvallinen Stripe-maksu – välitön kuitti ja lasku.',
                    'single' => [
                        'label' => 'Masteroi tämä kappale • :price',
                        'description' => 'Lataa WAV- ja MP3-masterit sekä stemmat tälle kappaleelle.',
                        'product_name' => 'Musicdistro Mastering – yksittäinen kappale',
                        'product_description' => 'Kertaluonteinen tekoälymasterointi 7 päivän korjausikkunalla.',
                    ],
                    'subscription' => [
                        'label' => 'Rajaton masterointi • :price_month/kk laskutetaan vuosittain (:price_year/vuosi)',
                        'description' => 'Rajattomat viennit, priorisoitu käsittely ja referenssivertailu.',
                        'product_name' => 'Musicdistro Mastering – rajaton vuosipassi',
                        'product_description' => '12 kuukautta rajatonta masterointia kaikille julkaisuille.',
                    ],
                    'processing_label' => 'Yhdistetään Stripeen…',
                    'success_redirect' => 'Ohjataan Stripe Checkoutiin…',
                    'generic_error' => 'Stripe-maksun käynnistäminen epäonnistui. Yritä uudelleen.',
                    'missing_key' => 'Stripe ei ole vielä käytössä. Ota yhteyttä tukeen.',
                    'success_single' => 'Maksu vahvistettu! Masterisi toimitetaan hetken kuluttua.',
                    'success_yearly' => 'Tervetuloa rajattomaan masterointiin – vuosipassisi on aktivoitu.',
                    'cancel_single' => 'Maksu keskeytettiin. Sinua ei veloitettu.',
                    'cancel_yearly' => 'Tilauksen maksu keskeytettiin. Sinua ei veloitettu.',
                    'disabled' => 'Masteroinnin maksut on poistettu käytöstä ylläpidon toimesta.',
                ],
                'download' => [
                    'label' => 'Lataa masteroitu tiedosto',
                    'description' => 'Vie masteroitu WAV suoraan selaimestasi – maksua ei vaadita.',
                    'hint' => 'Välitön vienti korkearesoluutioisessa WAV-muodossa.',
                    'processing' => 'Renderöidään masteroitua WAV-tiedostoa…',
                    'success' => 'Masteri valmis – lataus käynnistyy hetken kuluttua.',
                    'error' => 'Masterin vienti epäonnistui. Yritä uudelleen.',
                    'unsupported' => 'Masterin vienti ei ole tuettu tässä selaimessa. Kokeile Chromea tai Edgeä.',
                    'unavailable' => 'Pudota kappale ja anna tekoälyn viimeistellä masterointi ennen vientiä.',
                ],
            ],
            'smartlinks_modal' => [
                'badge' => 'Beta',
                'title' => 'Musicdistro Smartlinks',
                'description' => 'Luo monialustaiset laskeutumissivut sekunneissa ja seuraa klikkauksia, konversioita ja sijainteja – Linkfire-vaihtoehto suoraan hallintapaneelissa.',
                'tabs' => [
                    'create' => 'Luo smartlink',
                    'analytics' => 'Analytiikka ja historia',
                ],
                'form' => [
                    'upc_label' => 'UPC tai julkaisun tunnus',
                    'upc_placeholder' => 'esim. 123456789012',
                    'slug_label' => 'Mukautettu URL (valinnainen)',
                    'slug_placeholder' => 'oma-albumilanseeraus',
                    'slug_hint' => 'Jätä tyhjäksi, jos haluat automaattisesti siistin URL-osoitteen.',
                    'platforms_label' => 'Yhdistetyt palvelut',
                    'platforms_hint' => 'Valitse palvelut, jotka näytetään laskeutumissivulla.',
                    'submit' => 'Luo smartlink',
                    'processing' => 'Luodaan smartlinkiä…',
                ],
                'preview' => [
                    'title' => 'Esikatselu',
                    'subtitle' => 'Jaa alla oleva linkki, jotta fanit voivat valita suosikkipalvelunsa.',
                    'share_label' => 'Jaettava linkki',
                    'copy' => 'Kopioi linkki',
                    'copied' => 'Kopioitu!',
                    'empty' => 'Smartlinkin esikatselu ilmestyy tähän, kun se on luotu.',
                    'cta_label' => 'Kuuntele palvelussa',
                ],
                'success' => [
                    'title' => 'Smartlink valmis jaettavaksi',
                    'message' => 'Laskeutumissivu on julkaistu. Kopioi linkki ja seuraa tuloksia Analytiikka-välilehdeltä.',
                ],
                'history' => [
                    'title' => 'Viimeisimmät smartlinkit',
                    'empty' => 'Smartlinkkejä ei vielä ole. Luo ensimmäinen yllä.',
                    'created' => 'Luotu :date',
                    'analytics' => 'Näytä analytiikka',
                    'delete' => 'Poista',
                    'confirm_delete' => 'Poistetaanko smartlink " :name"? Tallennetut analytiikat poistetaan.',
                    'deleted' => 'Smartlink poistettu.',
                ],
                'analytics' => [
                    'title' => 'Suorituskyky',
                    'empty' => 'Luo smartlink saadaksesi analytiikan käyttöön.',
                    'selector_label' => 'Smartlinkkisi',
                    'summary' => [
                        'clicks' => 'Klikkaukset yhteensä',
                        'ctr' => 'Klikkausprosentti',
                        'conversions' => 'Suoratoistokonversiot',
                        'saves' => 'Tallennukset & ennakkotilaukset',
                    ],
                    'charts' => [
                        'traffic' => 'Liikenne ajan myötä',
                        'geography' => 'Sijainnit',
                        'platforms' => 'Palvelujen jakautuminen',
                    ],
                ],
                'errors' => [
                    'upc_required' => 'Anna UPC tai julkaisutunnus.',
                    'slug_invalid' => 'Mukautettu URL voi sisältää vain kirjaimia, numeroita ja viivoja.',
                    'request_failed' => 'Smartlinkin luominen epäonnistui. Yritä uudelleen.',
                ],
            ],
            'payments' => [
                'title' => 'Maksuhistoria',
                'subtitle' => 'Tarkista uusimmat maksut ja laskut.',
                'empty' => 'Maksutietoja ei ole vielä saatavilla.',
                'headers' => [
                    'date' => 'Päivä',
                    'description' => 'Kuvaus',
                    'amount' => 'Summa',
                    'status' => 'Tila',
                    'invoice' => 'Lasku',
                ],
                'status' => [
                    'paid' => 'Maksettu',
                    'pending' => 'Kesken',
                    'failed' => 'Epäonnistui',
                    'refunded' => 'Hyvitetty',
                ],
                'actions' => [
                    'download_invoice' => 'Lataa lasku',
                ],
            ],
            'checkout' => [
                'title' => 'Stripe Checkout',
                'unexpected_error' => 'Stripe Checkoutin käynnistäminen epäonnistui.',
                'processing' => 'Yhdistetään Stripeen…',
                'success' => 'Yhdistetty Stripeen.',
                'error' => 'Stripe-yhteys epäonnistui.',
            ],
            'footer' => '© :year :site. Vahvista tunne, älä byrokratiaa.',
            'js' => [
                'unexpected_admin' => 'Ylläpito-endpoint vastasi odottamattomasti.',
                'unexpected_profile' => 'Profiilin päivitys palautti odottamattoman vastauksen.',
                'admin_error' => 'Toimintoa ei voitu suorittaa. Yritä uudelleen.',
                'admin_success' => 'Toiminto suoritettu onnistuneesti.',
                'read_file_error' => 'Tiedostoa ei voitu lukea.',
                'load_image_error' => 'Kuvaa ei voitu ladata.',
                'process_image_error' => 'Kuvan käsittely ei onnistu tässä selaimessa.',
                'compress_image_error' => 'Kuvan pakkauksessa tapahtui virhe.',
            ],
        ],
    'publishing_modal' =>
    array (
      'badge' => 'Publishing administration',
      'title' => 'Collect more royalties worldwide',
      'subtitle' => 'Songwriters earn more when we register every work, protect your copyrights and chase global collections for you.',
      'hero_highlight' => 'Trusted by independent writers and catalogues in 80+ countries.',
      'cta_primary' =>
      array (
        'label' => 'Get started now',
        'href' => '/publishing',
      ),
      'cta_secondary' =>
      array (
        'label' => 'Book a call with our team',
        'href' => 'mailto:publishing@musicdistro.io',
      ),
      'stats' =>
      array (
        0 =>
        array (
          'value' => '85%',
          'label' => 'Royalties paid to you',
        ),
        1 =>
        array (
          'value' => '200+',
          'label' => 'Societies & DSPs',
        ),
        2 =>
        array (
          'value' => '24h',
          'label' => 'Registration turnaround',
        ),
      ),
      'highlights_title' => 'What we do for songwriters',
      'highlights' =>
      array (
        0 =>
        array (
          'title' => 'Global work registration',
          'description' => 'Register your compositions across PROs, CMOs and the MLC so nothing is left unclaimed.',
        ),
        1 =>
        array (
          'title' => 'Neighbouring rights & sync',
          'description' => 'Claim neighbouring rights, TV & film royalties and pitch your songs for premium sync placements.',
        ),
        2 =>
        array (
          'title' => 'Analytics & split management',
          'description' => 'Track real-time statements, manage splits transparently and automate co-writer payouts.',
        ),
      ),
      'royalties' =>
      array (
        'title' => 'Collect more royalties',
        'subtitle' => 'Keep 100% of your copyrights and unlock new revenue streams without the admin.',
        'points' =>
        array (
          0 =>
          array (
            'title' => 'Streaming optimisation',
            'description' => 'Earn up to 20% more from Spotify and Apple Music thanks to worldwide collections and dispute management.',
          ),
          1 =>
          array (
            'title' => 'Media & live performance',
            'description' => 'Capture royalties from venues, radio, TV, festivals and social platforms in 70+ territories.',
          ),
          2 =>
          array (
            'title' => 'Creative services',
            'description' => 'Dedicated admin team, simplified cue sheets and sync-ready metadata for every song.',
          ),
        ),
      ),
      'pricing' =>
      array (
        'title' => 'Publishing pricing',
        'subtitle' => 'Simple onboarding, no hidden fees.',
        'cards' =>
        array (
          0 =>
          array (
            'value' => '$75',
            'label' => 'One-time setup fee',
            'description' => 'Unlimited work registrations and catalogues.',
          ),
          1 =>
          array (
            'value' => '15%',
            'label' => 'Collection commission',
            'description' => 'Keep 85% of the royalties we collect on your behalf.',
          ),
          2 =>
          array (
            'value' => '50%',
            'label' => 'Sync placements',
            'description' => 'Optional. Split only when we secure a sync deal for you.',
          ),
        ),
      ),
      'testimonial' =>
      array (
        'label' => 'Songwriter testimonial',
        'quote' => '“Musicdistro handles the paperwork so I can focus on writing. Collections have jumped without me chasing societies.”',
        'author' => 'Lena Martínez – Songwriter & producer',
      ),
      'footnote' => 'Ready to onboard your catalogue? Our publishing specialists migrate your works in under 10 days.',
      'actions' =>
      array (
        'close' => 'Close',
      ),
    ),
    'home' => [
            'title' => 'MusicDistro.io – Jaa musiikkisi tekoälyn avulla',
            'meta' => [
                'description' => 'MusicDistro.io on tekoälyn ohjaama digitaalisen musiikin jakelualusta, joka auttaa artisteja, levy-yhtiöitä ja managereita muuttamaan jokaisen julkaisun globaaliksi menestykseksi. Julkaise kappaleesi, synkronoi markkinointikampanjat ja pidä 100 % rojalteistasi.',
                'keywords' => [
                    'digitaalinen musiikin jakelu',
                    'tekoälymusiikin jakelija',
                    'musiikin jakelupalvelu',
                    'artistien suoratoistoalusta',
                    'automaattinen musiikkimarkkinointi',
                    'musicdistro.io',
                ],
                'og_title' => 'MusicDistro.io – Tekoälyavusteinen musiikkijakelu rohkeille artisteille',
                'og_description' => 'Julkaise kappaleesi, synkronoi markkinointikampanjat ja säilytä rojaltisi MusicDistro.io:n avulla.',
                'twitter_title' => 'MusicDistro.io – Tekoälyavusteinen musiikkijakelu rohkeille artisteille',
                'twitter_description' => 'Julkaise kappaleesi, synkronoi markkinointikampanjat ja säilytä rojaltisi MusicDistro.io:n avulla.',
                'structured' => [
                    'service_type' => 'Tekoälyn ohjaama digitaalinen musiikkijakelu',
                    'area_served' => 'Maailmanlaajuisesti',
                    'offers' => [
                        [
                            '@type' => 'Offer',
                            'name' => 'Maailmanlaajuinen jakelu',
                            'price' => '0.00',
                            'priceCurrency' => 'EUR',
                            'availability' => 'https://schema.org/InStock',
                        ],
                        [
                            '@type' => 'Offer',
                            'name' => 'Tekoälymarkkinointikampanjat',
                            'price' => '29.00',
                            'priceCurrency' => 'EUR',
                            'availability' => 'https://schema.org/PreOrder',
                        ],
                    ],
                ],
            ],
            'nav' => [
                'brand_aria' => 'Palaa MusicDistro-esittelyyn',
                'toggle_open' => 'Avaa navigaatio',
                'toggle_close' => 'Sulje navigaatio',
                'menu_heading' => [
                    'badge' => 'Navigaatio',
                    'title' => 'Tutustu MusicDistro.io -ekosysteemiin',
                    'description' => 'Jakelu-, markkinointi- ja analytiikkatyökalut tukemaan tulevia julkaisuja.',
                ],
                'links' => [
                    'mission' => 'Missiomme',
                    'features' => 'Ominaisuudet',
                    'ai' => 'MusicPulse AI',
                    'faq' => 'UKK',
                ],
                'cta' => [
                    'register' => 'Luo tili',
                    'login' => 'Kirjaudu sisään',
                    'dashboard' => 'Hallintapaneeli',
                ],
                'meta' => [
                    'availability' => 'Saatavilla 24/7',
                    'contact' => 'contact@musicdistro.io',
                ],
            ],
            'hero' => [
                'eyebrow' => 'ENSIMMÄINEN TEKOÄLYLLÄ OHJATTU MUSIIKIN JAKELU',
                'typewriter_phrases' => [
                    'Julkaise musiikkisi kaikilla alustoilla.',
                    'Aktivoi fanisi ja kerää jokainen striimi.',
                    'Tekoäly ohjaa nousuasi levy-yhtiötä paremmin!',
                ],
                'subtitle' => 'MusicDistro.io yhdistää kitkattoman maailmanlaajuisen jakelun kasvua tukevan älykkyyden kanssa, jotta jokaisesta julkaisusta tulee koordinoitu lanseeraus. Studiolta soittolistoille kaikki pysyy synkronoituna yleisösi kasvattamiseksi.',
                'cta' => [
                    'primary' => 'Aloita maksutta',
                    'secondary' => 'Kirjaudu sisään',
                ],
                'card' => [
                    'aria_label' => 'Jakelukumppanit',
                    'badge' => 'DSP-HUBI',
                    'title' => 'Kappaleesi jokaisella alustalla',
                    'subtitle' => 'Synkronoi julkaisusi suurimpiin DSP-palveluihin ja sosiaalisiin verkostoihin tekoälyputkemme avulla.',
                    'platforms' => [
                        'Spotify',
                        'Apple Music',
                        'TikTok',
                        'YouTube Music',
                        'Deezer',
                        'Amazon Music',
                    ],
                    'marquee' => [
                        'Spotify',
                        'Apple Music',
                        'TikTok',
                        'YouTube Music',
                        'Amazon Music',
                        'Deezer',
                        'Instagram Reels',
                        'Facebook Music',
                    ],
                ],
                'highlights' => [
                    [
                        'value' => '250+',
                        'description' => 'Yhteistyöalustaa: Spotify, Apple Music, TikTok, YouTube, Deezer, Amazon Music ja paljon muuta.',
                    ],
                    [
                        'value' => '0%',
                        'description' => 'Rojaltien osuus, jonka pidämme. Omistat masterit ja ohjaat kasvua.',
                    ],
                    [
                        'value' => '48h',
                        'description' => 'Priorisoitujen julkaisujen hyväksyntä tekoälyavusteisen laadunvarmistuksen ansiosta.',
                    ],
                ],
            ],
            'features' => [
                'title' => 'Täydellinen palvelu artisteille ja levy-yhtiöille, jotka haluavat johtaa',
                'items' => [
                    [
                        'title' => 'Välitön maailmanlaajuinen jakelu',
                        'description' => 'Lähetä kappaleesi yhdellä klikkauksella yli 250 premium-alustalle. Algoritmi tarkistaa metadata ja varmistaa vaatimustenmukaisuuden.',
                        'bullets' => [
                            'Massalataus vedä ja pudota -toiminnolla',
                            'Tarkka hallinta alueista ja julkaisun ajankohdista',
                            'Automaattinen ISRC/UPC-luonti tarvittaessa',
                        ],
                    ],
                    [
                        'title' => 'Räätälöidyt tekoälykampanjat',
                        'description' => 'Kielimallimme luovat soittolistapitchit, somesisällön, videoskriptit ja fanisähköpostit. Jokainen kampanja kalibroidaan yhteisösi kasvattamiseksi.',
                        'bullets' => [
                            'Reaaliaikainen inspiraatioiden ja trendien analyysi',
                            'Soittolista- ja mediasuosituksia, jotka sopivat tyyliisi',
                            'Automaattiset seurannat fanien reaktioiden perusteella',
                        ],
                    ],
                    [
                        'title' => 'Läpinäkyvät rojalit reaaliajassa',
                        'description' => 'Seuraa tuloja alueen, alustan ja kappaleen mukaan. Saat hälytyksiä, kun striimisi kiihtyvät ja voit käynnistää markkinointitoimia vasteeksi.',
                        'bullets' => [
                            'Yhdistetty hallintapaneeli turvallisella kertakirjautumisella',
                            'Nopeat maksut useissa valuutoissa ja vastaanottajille',
                            'Kirjanpitoon sopivat viennit ja kumppanivalmiit raportit',
                        ],
                    ],
                ],
            ],
            'ai' => [
                'title' => 'MusicPulse AI: komentokeskuksesi kasvuun',
                'items' => [
                    [
                        'title' => 'Julkaisuskriptit',
                        'description' => 'MusicPulse analysoi inspiraatiosi, somen keskustelut ja hakupiikit määrittääkseen parhaan julkaisuhetken.',
                    ],
                    [
                        'title' => 'Fani-kaksoset',
                        'description' => 'Tunnista yleisöt, jotka muistuttavat nykyisiä faneja, ja saa ehdotuksia yhteistyöstä, soittolistoista ja mediapaikoista.',
                    ],
                    [
                        'title' => 'Momentum-hälytykset',
                        'description' => 'Saat ilmoituksen heti, kun avainsoittolista lisää kappaleesi, jokin maa kiihtyy tai UGC sisältö alkaa levitä.',
                    ],
                ],
            ],
            'testimonials' => [
                'title' => 'He muuttivat julkaisunsa näkyviksi voitoiksi',
                'prev' => 'Edellinen suositus',
                'next' => 'Seuraava suositus',
                'dots_aria' => 'Suosittelusivutus',
                'dot_label' => 'Näytä suositus :number',
                'items' => [
                    [
                        'quote' => '« MusicDistro.io:n ansiosta kasvatin yleisöäni 60 % kolmessa kuukaudessa edelliseen jakelijaani verrattuna. Tekoälyn suositukset kohdistivat oikeat paikalliset soittolistat. »',
                        'author' => 'NAOMI LYS – R&B-laulaja',
                    ],
                    [
                        'quote' => '« Puheet sikseen – MusicDistro.io:n tekoäly muutti kaiken ja kiihdytti uraani. Jokainen julkaisu on käsikirjoitettu ja striimit räjähtävät ensimmäisestä päivästä. »',
                        'author' => 'DJ NOVEL – Elektroninen tuottaja',
                    ],
                    [
                        'quote' => '« Siirtyminen uudelle jakelijalle kolminkertaisti ennakkotallennukset. MusicDistro.io:n automatisoidut sekvenssit pitävät fanit jännityksessä julkaisuun asti. »',
                        'author' => 'BLACKWAVE COLLECTIVE – Indie-levy-yhtiö',
                    ],
                    [
                        'quote' => '« MusicPulse tuotti soittolistapitchit, joiden ansiosta pääsimme viidelle toimitukselliselle soittolistalle ensimmäisellä viikolla ilman lisä-PR:ää. »',
                        'author' => 'LINA ORTEGA – Manageri',
                    ],
                    [
                        'quote' => '« Reaaliaikainen hallintapaneeli antaa säätää jokaista mainoskampanjaa ennen päivän päättymistä. Muutamme ennakkotilauksia kaksinkertaisella teholla viime vuoteen verrattuna. »',
                        'author' => 'MANELI CRUZ – Artistimanageri',
                    ],
                    [
                        'quote' => '« Tekoälyn ohjaamat TikTok-kampanjamme tuottavat 40 % enemmän UGC-sisältöä. MusicDistro.io korvaa kolme aiempaa markkinointityökalua. »',
                        'author' => 'PULSEWAVE RECORDS – Indie-yhtiö',
                    ],
                    [
                        'quote' => '« MusicDistro.io järjesti maailmalaajuisen yllätysjulkaisun 48 tunnissa monikielisen tarinankerronnan avulla. Yhteisömme aktivoitui välittömästi. »',
                        'author' => 'AYA NOVA – Indie pop -artisti',
                    ],
                    [
                        'quote' => '« Kilpailija-analyysi auttoi valitsemaan oikeat vierailijat ja kolminkertaisti kuukausittaiset toistomme Spotifyssa ja Deezerissä. »',
                        'author' => 'ORION ATLAS – Alternative pop -duo',
                    ],
                    [
                        'quote' => '« Momentum-hälytykset kertovat heti, kun tärkeä soittolista lisää kappaleeni. Aktivoin fanit tunnin sisällä ja pidän nosteen yllä vaivatta. »',
                        'author' => 'KEZZA – Räppäri',
                    ],
                    [
                        'quote' => '« Tekoälysuosittelujen avulla avasimme kaksi uutta kansainvälistä markkinaa ja tuplasimme verkkokaupan liikevaihdon kuudessa kuukaudessa. »',
                        'author' => 'STELLAR SOUND – Boutique-levy-yhtiö',
                    ],
                ],
            ],
            'faq' => [
                'title' => 'Usein kysytyt kysymykset',
                'entries' => [
                    [
                        'question' => 'Mikä on MusicDistro.io?',
                        'answer' => 'MusicDistro.io on tekoälyn ohjaama digitaalisen musiikin jakelualusta. Se toimittaa kappaleesi yli 250 palveluun, automatisoi markkinointikampanjasi ja seuraa rojalteja reaaliajassa.',
                    ],
                    [
                        'question' => 'Miten MusicDistro.io:n tekoäly toimii?',
                        'answer' => 'Moottorimme analysoi metadatasi, inspiraatiot ja globaalit trendit ehdottaakseen julkaisustrategioita, luodakseen promootiokopion, tunnistaakseen kohdennetut soittolistat ja laukaistakseen hälytyksiä aina, kun yleisösi reagoi.',
                    ],
                    [
                        'question' => 'Paljonko MusicDistro.io:n jakelu maksaa?',
                        'answer' => 'Rekisteröityminen on maksutonta. Toimimme läpinäkyvällä tulonjaolla, pidämme 0 % rojalteistasi ja tarjoamme valinnaisia premium-buusteja promootion kiihdyttämiseksi tai kehittyneiden analytiikoiden avaamiseksi.',
                    ],
                    [
                        'question' => 'Voinko tuoda olemassa olevan katalogini?',
                        'answer' => 'Kyllä. MusicDistro.io tukee olemassa olevia katalogeja CSV-tuonnin, kumppani-integraatioiden ja API-synkronoinnin avulla. Pidät ISRC-/UPC-tunnisteesi ja striimihistoriasi.',
                    ],
                    [
                        'question' => 'Millaista tukea tarjoatte levy-yhtiöille ja managereille?',
                        'answer' => 'Jakelun lisäksi tarjoamme monen artistin raportointityökalut, trendihälytykset, sponsoroidut hankintakampanjat sekä omistautuneen monikielisen tuen ammattilaisille.',
                    ],
                ],
            ],
            'cta' => [
                'title' => 'Valmis johtamaan listoja?',
                'body' => 'Liity MusicDistro.io:hon, aktivoi tilisi muutamassa minuutissa ja anna tekoälyn kuljettaa musiikkisi faneille, jotka jo odottavat sinua.',
                'primary' => 'Luo tilini',
                'secondary' => 'Siirry työtilaani',
                'image_alt' => 'Kuva maailmanlaajuisesta listanoususta',
            ],
            'footer' => '© :year :site – Tekoälyjakelu, joka kasvattaa yleisöäsi.',
        ],
        'tutorial' => [
            'title' => 'Opas: jaa musiikkisi – :site',
            'header' => [
                'title' => 'Jaa musiikkisi luottavaisin mielin',
                'subtitle' => 'Tämä opas kuljettaa sinut viimeisestä miksauksesta alustan hyväksyntään. Seuraa jokaista vaihetta julkaistaksesi kappaleesi fanien ansaitsemalla tunteella.',
            ],
            'steps' => [
                [
                    'title' => 'Valmistele masterit ja visuaalit',
                    'description' => 'Kerää 24-bit / 48 kHz WAV-tiedostot, vie kansi 3000×3000 px -kokoisena ja varmista ID3-metadatan yhdenmukaisuus.',
                    'bullets' => [
                        'Tarkista artistien ja feattaajien kirjoitusasut.',
                        'Käytä häviöttömiä vientejä (ei MP3:a).',
                        'Valmistele sanoitukset, jos haluat synkronoida ne.',
                    ],
                ],
                [
                    'title' => 'Luo julkaisu Sonosuitessa',
                    'description' => 'Kirjaudu sisään hallintapaneelin kortista. Valitse Sonosuitessa ”Create album” ja anna avustajan ohjata.',
                    'bullets' => [
                        'Valitse julkaisun tyyppi: single, EP tai albumi.',
                        'Täytä levy-yhtiö, tekijät ja tekijänoikeusvuosi.',
                        'Lataa kappaleet ja lisää ISRC-/UPC-koodit, jos sinulla on ne.',
                    ],
                ],
                [
                    'title' => 'Optimoi lanseerausstrategia',
                    'description' => 'Ajoita julkaisupäivä vähintään 10 päivän päähän hyödyntääksesi Spotify for Artists -suosituksia. Aktivoi tekoälyn oivallukset räätälöidyn mediaplanin luomiseksi.',
                    'bullets' => [
                        'Aikatauluta TikTok-, Instagram Reels- ja YouTube Shorts -teaserit.',
                        'Suunnittele automaattiset fanisähköpostit ja soittolistapitchit.',
                        'Lisää eksklusiivisia alueita, jos kohdistat tietyille markkinoille.',
                    ],
                ],
                [
                    'title' => 'Seuraa tuloksia',
                    'description' => 'Kun julkaisu on verkossa, palaa Sonosuiteen seuraamaan striimejä, latauksia ja rojalteja. Tekoälymme ilmoittaa, kun kappaleesi saa vauhtia.',
                    'bullets' => [
                        'Analysoi eniten striimejä tuottavat soittolistat.',
                        'Tunnista maat, joissa fanit aktivoituvat ensin.',
                        'Avaa yhteistyösuosituksia yleisön samankaltaisuuden perusteella.',
                    ],
                ],
            ],
            'cta' => [
                'label' => 'Takaisin hallintapaneeliin',
                'guest_label' => 'Luo tili',
            ],
        ],
        'validation' => [
            'method_not_allowed' => 'Metodia ei sallita.',
            'json_invalid' => 'Virheellinen pyyntö: tuntematon JSON-kuorma.',
            'auth_required' => 'Kirjautuminen vaaditaan.',
            'first_name_required' => 'Etunimi on pakollinen.',
            'last_name_required' => 'Sukunimi on pakollinen.',
            'email_invalid' => 'Anna kelvollinen sähköpostiosoite.',
            'email_required' => 'Sähköpostiosoite on pakollinen.',
            'email_exists' => 'Tällä sähköpostilla on jo tili. Kirjaudu sisään tai pyydä uusi vahvistuslinkki.',
            'email_unverified' => 'Vahvista sähköpostisi ennen kirjautumista. Tarkista postisi tai ota yhteyttä tukeen.',
            'country_required' => 'Valitse maa.',
            'country_invalid' => 'Valitse kelvollinen maa.',
            'role_required' => 'Valitse ammatillinen profiilisi.',
            'business_type_invalid' => 'Valitse kelvollinen tilin tyyppi.',
            'phone_invalid' => 'Anna kelvollinen puhelinnumero (numerot, välilyönnit ja merkit .()+-).',
            'company_name_required' => 'Anna yrityksen nimi.',
            'password_required' => 'Salasana on pakollinen.',
            'password_min' => 'Salasanassa tulee olla vähintään 8 merkkiä.',
            'password_confirmation' => 'Salasanat eivät täsmää.',
            'language_invalid' => 'Valitse kieli listalta.',
            'credentials_invalid' => 'Virheelliset tunnukset. Yritä uudelleen tai luo tili.',
            'token_invalid' => 'Tämä tunniste on virheellinen.',
            'avatar_storage_failed' => 'Profiilikuvan tallennustilan valmistelu epäonnistui.',
            'avatar_upload_failed' => 'Kuvan lataus epäonnistui.',
            'avatar_size' => 'Profiilikuvan koko ei saa ylittää 5 MB.',
            'avatar_format' => 'Sallitut muodot: JPEG, PNG tai WebP.',
            'avatar_save_failed' => 'Profiilikuvan tallennus epäonnistui.',
            'user_not_found' => 'Käyttäjää ei löydy.',
            'action_missing' => 'Toiminto tai käyttäjätunnus puuttuu.',
            'cannot_self_manage' => 'Et voi suorittaa tätä toimintoa omalle tilillesi.',
            'cannot_manage_super_admin' => 'Tätä toimintoa ei sallita pääylläpitäjälle.',
            'account_deleted' => 'Tili on poistettu.',
            'account_blocked' => 'Käyttäjän pääsy estetty.',
            'account_already_blocked' => 'Tili on jo estetty.',
            'account_unblocked' => 'Käyttäjän pääsy palautettu.',
            'account_already_active' => 'Tili on jo aktiivinen.',
            'unknown_action' => 'Tuntematon toiminto.',
            'distribution_provider_invalid' => 'Valitse kelvollinen jakeluhallintapaneeli.',
            'sonosuite_base_url_invalid' => 'Anna kelvollinen Sonosuite-alustan URL (https://).',
            'sonosuite_shared_secret_required' => 'Anna Sonosuiten SSO-yhteinen salaisuus.',
        ],
    ]
);
