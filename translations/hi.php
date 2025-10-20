<?php

return array_replace_recursive(require __DIR__ . '/en.php', [
    'language' => [
        'label' => 'भाषा',
        'menu_label' => 'भाषा बदलें',
        'choose_language' => 'अपनी भाषा चुनें',
        'close_menu' => 'भाषा मेनू बंद करें',
        'updated' => 'आपकी भाषा प्राथमिकता अपडेट कर दी गई है (:language)।',
    ],
    'alerts' => [
        'blocked_access' => 'आपकी पहुंच सीमित कर दी गई है।',
    ],
    'email' => [
        'common' => [
            'greeting' => 'नमस्ते :name,',
            'greeting_generic' => 'नमस्ते,',
            'signature' => 'जल्द मिलते हैं,\\n:site टीम',
        ],
        'verification' => [
            'subject' => ':site के लिए अपना ईमेल पुष्टि करें',
            'intro' => ':site में आपका स्वागत है! अपना खाता सक्रिय करने और अपना संगीत वितरित करना शुरू करने के लिए नीचे दिए गए लिंक का उपयोग करें।',
            'action' => 'सत्यापन लिंक: :link',
            'footer' => 'यदि आपने यह पंजीकरण अनुरोध नहीं किया है, तो आप इस ईमेल को अनदेखा कर सकते हैं।',
        ],
        'reset' => [
            'subject' => 'अपना :site पासवर्ड रीसेट करें',
            'intro' => 'आपने :site पर अपना पासवर्ड रीसेट करने का अनुरोध किया था।',
            'action' => 'नया पासवर्ड चुनने के लिए यह लिंक खोलें: :link',
            'expiration' => 'यह लिंक 60 मिनट में समाप्त हो जाएगा। यदि आपने यह अनुरोध नहीं किया है, तो आप इस संदेश को सुरक्षित रूप से अनदेखा कर सकते हैं।',
        ],
    ],
    'auth' => [
        'roles' => [
            'musician' => 'संगीतकार',
            'artist' => 'कलाकार',
            'manager' => 'प्रबंधक',
            'producer' => 'निर्माता',
            'publisher' => 'प्रकाशक',
            'label' => 'लेबल',
            'other' => 'अन्य',
            'member' => 'सदस्य',
        ],
        'common' => [
            'first_name_label' => 'पहला नाम',
            'last_name_label' => 'अंतिम नाम',
            'email_label' => 'ईमेल पता',
            'country_label' => 'निवास देश',
            'role_label' => 'आपकी प्रोफ़ाइल',
            'language_label' => 'पसंदीदा भाषा',
            'password_label' => 'पासवर्ड',
            'confirm_password_label' => 'पासवर्ड की पुष्टि करें',
        ],
        'login' => [
            'title' => 'साइन इन करें',
            'lead' => 'अपने डैशबोर्ड पर वापस जाएं और अपनी पहचान को निरंतर बढ़ाते रहें।',
            'submit' => 'साइन इन करें',
            'forgot' => 'पासवर्ड भूल गए?',
            'register_prompt' => 'अभी तक खाता नहीं है? :link।',
            'register_link' => 'साइन अप करें',
        ],
        'register' => [
            'intro_title' => ':site से जुड़ें और एआई को आपकी आवाज़ को बढ़ाने दें।',
            'intro_text' => 'हम हर कलाकार को उस दर्शक से जोड़ते हैं जो पहले से ही उनके संगीत का इंतजार कर रहा है। कुछ ही मिनटों में अपना पहला रिलीज़ लॉन्च करने के लिए साइन अप करें।',
            'bullets' => [
                'native_ai' => 'आपके अभियानों, प्लेलिस्ट पिचों और प्रशंसक अंतर्दृष्टि के लिए मूल एआई मार्केटिंग।',
                'worldwide' => '250 से अधिक प्रीमियम प्लेटफ़ॉर्म पर त्वरित वैश्विक वितरण।',
                'royalties' => 'पारदर्शी ट्रैकिंग और वास्तविक समय अलर्ट के साथ अपनी 100% रॉयल्टी रखें।',
            ],
            'title' => 'अपना खाता बनाएं',
            'lead' => 'अपना पुष्टिकरण लिंक प्राप्त करने के लिए अपनी जानकारी साझा करें।',
            'language_help' => 'हम डैशबोर्ड, ईमेल और ऑनबोर्डिंग को इसी भाषा में वैयक्तिकृत करेंगे।',
            'submit' => 'मेरा खाता सक्रिय करें',
            'login_prompt' => 'पहले से सदस्य हैं? :link।',
            'login_link' => 'साइन इन करें',
            'success' => 'धन्यवाद! अपना पता पुष्टि करने और खाता सक्रिय करने के लिए अपना इनबॉक्स देखें।',
        ],
        'forgot' => [
            'title' => 'क्या आप अपना पासवर्ड भूल गए?',
            'lead' => 'रीसेट निर्देश प्राप्त करने के लिए अपना ईमेल पता दर्ज करें।',
            'submit' => 'रीसेट लिंक भेजें',
            'back_to_login' => 'साइन इन पर लौटें',
            'success' => 'यदि इस ईमेल पते से कोई खाता मेल खाता है, तो हमने आपको पासवर्ड रीसेट करने के निर्देश भेज दिए हैं।',
        ],
        'reset' => [
            'title' => 'नया पासवर्ड चुनें',
            'lead' => 'अपना खाता फिर से सुरक्षित करने के लिए एक मजबूत पासवर्ड चुनें।',
            'submit' => 'पासवर्ड अपडेट करें',
            'token_invalid' => 'यह रीसेट लिंक अवैध या अपूर्ण है। नया लिंक अनुरोध करें।',
            'token_expired' => 'यह रीसेट लिंक समाप्त हो चुका है। नया लिंक अनुरोध करें।',
            'token_used' => 'यह रीसेट लिंक अब मान्य नहीं है। नया लिंक अनुरोध करें।',
            'success' => 'आपका पासवर्ड अपडेट कर दिया गया है। अब आप साइन इन कर सकते हैं।',
            'new_password_label' => 'नया पासवर्ड',
            'confirm_password_label' => 'पासवर्ड की पुष्टि करें',
            'request_new_link' => 'नया लिंक अनुरोध करें',
            'return_to_login' => 'साइन इन पर लौटें',
        ],
        'verify' => [
            'expired_title' => 'समाप्त या अवैध लिंक',
            'expired_body' => 'आपके द्वारा उपयोग किया गया सत्यापन लिंक अब मान्य नहीं है। नया लिंक प्राप्त करने के लिए :email पर हमारी सहायता टीम से संपर्क करें।',
            'cta_login' => 'साइन इन पर लौटें',
            'success' => 'आपका ईमेल पता पुष्टि हो गया है। अब आप साइन इन कर सकते हैं।',
        ],
        'blocked' => [
            'title' => 'पहुंच प्रतिबंधित',
            'lead' => 'मदद चाहिए? :email पर लिखें, हमारी टीम आपको तुरंत सहायता देगी।',
            'cta_login' => 'साइन इन पर लौटें',
        ],
        'profile' => [
            'updated' => 'परिवर्तन सहेजे गए।',
        ],
    ],
    'dashboard' => [
        'title' => 'डैशबोर्ड – :site',
        'brand_alt' => ':site डैशबोर्ड',
        'profile_panel' => [
            'title' => 'आपकी प्रोफ़ाइल',
            'helper' => 'अपनी पहचान, देश, भाषा और प्रोफ़ाइल फोटो अपडेट करें ताकि आपकी उपस्थिति एक समान रहे।',
            'remove_photo' => 'फोटो हटाएं',
            'remove_photo_sr' => 'फोटो हटाएं',
            'change_photo' => 'फोटो बदलें',
            'preview_alt' => 'प्रोफ़ाइल फोटो पूर्वावलोकन',
            'photo_alt' => 'प्रोफ़ाइल फोटो',
            'close' => 'पैनल बंद करें',
            'labels' => [
                'first_name' => 'पहला नाम',
                'last_name' => 'अंतिम नाम',
                'country' => 'देश',
                'role' => 'व्यावसायिक प्रोफ़ाइल',
                'language' => 'भाषा',
                'address_line1' => 'पता पंक्ति 1',
                'address_line2' => 'पता पंक्ति 2',
                'postal_code' => 'डाक कोड',
                'city' => 'शहर',
                'phone_number' => 'फोन नंबर',
                'business_type' => 'खाते का प्रकार',
                'company_name' => 'कंपनी का नाम',
                'company_vat' => 'वैट / टैक्स आईडी',
            ],
            'language_help' => 'हम डैशबोर्ड और सूचनाओं को इसी भाषा में वैयक्तिकृत करेंगे।',
            'business_type_helper' => 'चुनें कि आप व्यक्तिगत रूप से कार्य कर रहे हैं या किसी कंपनी की ओर से।',
            'business_fields_helper' => 'कंपनी के विवरण चालानों और एडमिन निर्यात में दिखाई देंगे।',
            'business_type_options' => [
                'individual' => 'व्यक्ति',
                'company' => 'कंपनी',
            ],
            'submit' => 'परिवर्तन सहेजें',
            'submit_processing' => 'सहेजा जा रहा है…',
            'logout' => 'लॉग आउट',
            'feedback' => [
                'saving' => 'सहेजा जा रहा है…',
                'image_optimizing' => 'आपकी छवि को अनुकूलित किया जा रहा है…',
                'image_ready' => 'छवि सहेजने के लिए तैयार है।',
                'image_error' => 'आपकी छवि को संसाधित करते समय त्रुटि हुई।',
                'photo_removed' => 'फोटो हटाई गई। सहेजना न भूलें।',
                'profile_refresh' => 'आपकी प्रोफ़ाइल अपडेट हो गई है। रीफ़्रेश किया जा रहा है…',
                'profile_success' => 'आपकी प्रोफ़ाइल सफलतापूर्वक अपडेट हुई है।',
                'profile_error' => 'आपकी प्रोफ़ाइल अपडेट नहीं हो सकी।',
                'unexpected_error' => 'एक अप्रत्याशित त्रुटि हुई। कृपया पुनः प्रयास करें।',
            ],
        ],
        'welcome' => [
            'title' => 'नमस्ते :name, दुनिया को झकझोरने के लिए तैयार हैं?',
            'body' => 'आपका कार्यक्षेत्र उन सभी टूल्स को केंद्रीकृत करता है जो आपके ट्रैक्स को वायरल अनुभवों में बदलते हैं। रिलीज़ तैयार करें, अपने वितरण का प्रबंधन करें और हर प्रशंसक इंटरैक्शन को रियल टाइम में ट्रैक करें।',
        ],
        'studio_card' => [
            'aria_label' => 'MusicDistro स्टूडियो लॉन्च करें',
            'badge' => 'नया',
            'title' => 'ब्राउज़र में बनाएं, मिक्स करें और मास्टर करें',
            'subtitle' => 'टाइमलाइन, मिक्सर, प्लगइन और रियल टाइम एक्सपोर्ट के साथ प्रो-ग्रेड म्यूज़िक स्टूडियो खोलें।',
            'cta' => 'म्यूज़िक स्टूडियो खोलें',
        ],
        'cards' => [
            'distribution' => [
                'title' => 'संगीत वितरण',
                'description' => 'नई रिलीज़ लॉन्च करें, एल्बम बनाएं और जुड़े वितरण कंसोल से रॉयल्टी की निगरानी करें। पूरा डिलीवरी चेन एक क्लिक में उपलब्ध है।',
                'link_label' => 'वितरण कंसोल खोलें',
            ],
            'tutorial' => [
                'title' => 'क्रमबद्ध वितरण ट्यूटोरियल',
                'description' => 'मास्टर्स तैयार करने, मेटाडेटा कॉन्फ़िगर करने और रिलीज़ दिन पर प्रभाव बढ़ाने के लिए हमारे दृश्य मार्गदर्शक का पालन करें।',
                'link_label' => 'ट्यूटोरियल देखें',
            ],
            'royalties' => [
                'title' => 'अपनी 100% रॉयल्टी एकत्रित करें',
                'description' => 'Spotify, Apple Music, YouTube, Amazon Music और 150+ DSP पर प्रीमियम भुगतान एक ही अपग्रेड से अनलॉक करें।',
                'link_label' => 'प्रीमियम प्लान जानें',
            ],
            'mastering' => [
                'title' => 'इन-हाउस एआई मास्टरिंग',
                'description' => 'कुछ ही मिनटों में रेडियो-रेडी लाउडनेस और स्पष्टता पाएं। अपना मिक्स छोड़ें, प्रो प्रीसेट खोजें और तुरंत निर्यात करें।',
                'link_label' => 'मास्टरिंग स्टूडियो लॉन्च करें',
            ],
            'smartlinks' => [
                'title' => 'परिवर्तित करने वाले स्मार्टलिंक',
                'description' => 'स्पॉटिफ़ाई, ऐप्पल म्यूज़िक, डिज़र, अमेज़ॅन म्यूज़िक, यूट्यूब म्यूज़िक और प्रत्येक जुड़े DSP की ओर प्रशंसकों को ले जाने वाले लैंडिंग पेज उत्पन्न करें।',
                'link_label' => 'स्मार्टलिंक प्रबंधित करें',
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
                'title' => 'कलाकारों के लिए Musicdistro क्लाउड',
                'description' => 'अपने मास्टर्स को एन्क्रिप्टेड वॉल्ट में संग्रहीत, टाइमस्टैम्प और साझा करें जो स्वामित्व के ब्लॉकचेन प्रमाण से सुरक्षित है।',
                'link_label' => 'Musicdistro क्लाउड जानें',
            ],
            'youtube' => [
                'title' => 'YouTube अधिकार प्रबंधन',
                'description' => 'YouTube कंटेंट आईडी सिंक्रोनाइज़ करें और अपने आधिकारिक कलाकार चैनलों को केंद्रीकृत संपत्तियों से जोड़ें।',
                'link_label' => 'YouTube कॉन्फ़िगर करें',
            ],
            'publishing_administration' => [
                'title' => 'पब्लिशिंग प्रशासन',
                'description' => 'रचनाओं को पंजीकृत करें, नेबरिंग राइट्स एकत्र करें और रॉयल्टी विभाजन को वास्तविक समय में ट्रैक करें।',
                'link_label' => 'पब्लिशिंग प्रबंधित करें',
            ],
            'royalties_withdrawal' => [
                'title' => 'रॉयल्टी निकासी',
                'description' => 'भुगतान शुरू करें, बैंक खाते चुनें और हर कैटलॉग के निकासी को एक ही स्थान से ट्रैक करें।',
                'link_label' => 'रॉयल्टी निकालें',
            ],
            'coaching' => [
                'title' => 'एआई कोचिंग और अभियान',
                'description' => 'एआई-संचालित अभियान सक्रिय करें, उपयुक्त प्लेलिस्ट पहचानें और व्यक्तिगत प्रोमो योजना प्राप्त करें।',
                'link_label' => 'जल्द आ रहा है',
                'alert' => 'एआई अभियान जल्द ही आ रहे हैं।',
            ],
            'payments' => [
                'title' => 'बिलिंग और भुगतान',
                'description' => 'हर Stripe भुगतान की समीक्षा करें, चालान डाउनलोड करें और आगामी नवीनीकरण पर नज़र रखें।',
                'link_label' => 'भुगतान इतिहास देखें',
            ],
        ],
        'admin' => [
            'title' => 'प्रशासन',
            'subtitle' => 'हर खाते की समीक्षा करें। ये अंतर्दृष्टियाँ केवल सुपर व्यवस्थापकों को दिखाई देती हैं।',
            'tabs' => [
                'users' => 'उपयोगकर्ता',
                'payments' => 'मॉनेटाइज़ेशन',
                'notifications' => 'सूचनाएँ',
                'newsletter' => 'न्यूज़लेटर',
                'configuration' => 'कॉन्फ़िगरेशन',
                'distribution' => 'वितरण',
            ],
            'stripe' => [
                'title' => 'Stripe कॉन्फ़िगरेशन',
                'description' => 'प्रीमियम सदस्यता और स्वचालित नवीनीकरण सक्रिय करने के लिए अपनी Stripe API कुंजियाँ जोड़ें।',
                'secret_label' => 'Stripe सीक्रेट कुंजी',
                'publishable_label' => 'Stripe प्रकाशित करने योग्य कुंजी (वैकल्पिक)',
                'helper' => 'हम कुंजियों को केवल आपके निजी डेटाबेस में एन्क्रिप्टेड रूप में संग्रहीत करते हैं। इन्हें कभी भी अपडेट करें।',
                'submit' => 'Stripe सेटिंग्स सहेजें',
                'feedback' => [
                    'processing' => 'Stripe सेटिंग्स सहेजी जा रही हैं…',
                    'saved' => 'Stripe सेटिंग्स सफलतापूर्वक अपडेट की गईं।',
                    'error' => 'Stripe सेटिंग्स सहेजी नहीं जा सकीं। कृपया अपनी कुंजियों की जाँच करें और पुनः प्रयास करें।',
                ],
            ],
            'monetization' => [
                'title' => 'मास्टरिंग मॉनेटाइज़ेशन',
                'description' => 'निर्धारित करें कि कलाकार स्टूडियो तक कैसे पहुँचते हैं और क्या Stripe चेकआउट आवश्यक है।',
                'currency_section_title' => 'बिलिंग मुद्राएँ',
                'currency_section_description' => 'सभी सशुल्क सेवाओं के लिए उपलब्ध मुद्राओं को कॉन्फ़िगर करें।',
                'payments_label' => 'मास्टरिंग निर्यात के लिए शुल्क लें',
                'payments_enabled' => 'Stripe चेकआउट कार्ड दिखाए जाएंगे और भुगतान के बाद ही उपयोगकर्ता मास्टर्स डाउनलोड कर सकेंगे।',
                'payments_disabled' => 'उपयोगकर्ता बिना भुगतान के तुरंत अपने मास्टर्स निर्यात कर सकते हैं।',
                'single_label' => 'एकल मास्टर कीमत',
                'single_hint' => 'वन-ऑफ मास्टरिंग कार्ड पर प्रदर्शित (EUR)।',
                'yearly_label' => 'वार्षिक मास्टरिंग पास कीमत',
                'yearly_hint' => 'प्रति वर्ष एक बार बिल की जाने वाली कुल राशि (EUR)।',
                'publishing_title' => 'Publishing administration',
                'publishing_description' => 'Set the one-off onboarding fee for publishing administration.',
                'publishing_price_label' => 'Publishing setup price',
                'publishing_price_helper' => 'Displayed on the publishing administration card (default currency).',
                'currency_suffix' => 'EUR',
                'invalid_price' => 'कोई मान्य मास्टरिंग कीमत दर्ज करें (उदाहरण: 9.99)।',
                'invalid_publishing_price' => 'Enter a valid publishing setup price (e.g. 75).',
            ],
            'cloud_storage' => [
                'title' => 'Musicdistro क्लाउड',
                'description' => 'सुरक्षित कलाकार क्लाउड सेवा के लिए मूल्य निर्धारण निर्धारित करें।',
                'usage' => [
                    'title' => 'उपयोग आधारित बिलिंग',
                    'description' => 'प्रति मेगाबाइट संग्रहीत और प्रति अपलोड फ़ाइल शुल्क लें।',
                    'toggle_label' => 'उपयोग आधारित बिलिंग सक्षम करें',
                    'price_mb_label' => 'प्रति एमबी मूल्य',
                    'price_mb_helper' => 'प्रति महीने औसत स्टोरेज आकार पर बिल किया जाता है।',
                    'price_file_label' => 'प्रति फ़ाइल मूल्य',
                    'price_file_helper' => 'फ़ाइल आकार की परवाह किए बिना प्रति अपलोड एक बार शुल्क लिया जाता है।',
                    'state_enabled' => 'उपयोग आधारित बिलिंग सक्रिय',
                    'state_disabled' => 'उपयोग आधारित बिलिंग निष्क्रिय',
                ],
                'subscription' => [
                    'title' => 'सदस्यता ऑफ़र',
                    'description' => 'शामिल स्टोरेज के साथ मासिक बंडल ऑफर करें।',
                    'toggle_label' => 'सदस्यता ऑफ़र सक्षम करें',
                    'price_label' => 'सदस्यता मूल्य',
                    'price_helper' => 'कलाकार से वसूली जाने वाली आवर्ती राशि।',
                    'storage_label' => 'शामिल स्टोरेज कोटा',
                    'storage_helper' => 'प्लान में कितने मेगाबाइट शामिल हैं, निर्धारित करें।',
                    'storage_suffix' => 'MB',
                    'state_enabled' => 'सदस्यता सक्रिय',
                    'state_disabled' => 'सदस्यता निष्क्रिय',
                ],
                'validation_usage' => 'उपयोग आधारित बिलिंग सक्षम करने के लिए प्रति एमबी और प्रति फ़ाइल कीमत प्रदान करें।',
                'validation_subscription' => 'ऑफ़र सक्रिय करने के लिए सदस्यता मूल्य और स्टोरेज कोटा सेट करें।',
            ],
            'notifications' => [
                'title' => 'डैशबोर्ड सूचनाएँ',
                'description' => 'उन सूचनाओं को नियंत्रित करें जो कलाकार डैशबोर्ड में दिखाई देती हैं।',
                'display_label' => 'सूचनाएँ आइकन दिखाएँ',
                'display_helper' => 'अक्षम होने पर डैशबोर्ड से घंटी आइकन छुपाएँ।',
                'automations_title' => 'स्वचालित अलर्ट',
                'automations_description' => 'उन स्वचालित अनुस्मारकों का चयन करें जो उपयोगकर्ताओं को भेजे जाएंगे।',
                'profile_incomplete_label' => 'उपयोगकर्ताओं को उनकी प्रोफ़ाइल जानकारी पूर्ण करने की याद दिलाएँ',
                'profile_incomplete_helper' => 'जब पता, देश या फ़ोन नंबर अनुपलब्ध हो तब अनुस्मारक भेजा जाता है।',
                'broadcast' => [
                    'title' => 'कस्टम प्रसारण',
                    'description' => 'हर कलाकार डैशबोर्ड पर एक व्यक्तिगत सूचना भेजें।',
                    'helper' => 'हर भाषा के लिए संदेश का अनुवाद करें। खाली फ़ील्ड अंग्रेज़ी संस्करण को अपनाते हैं।',
                    'link_label' => 'सूचना लिंक (वैकल्पिक)',
                    'link_placeholder' => 'https://musicdistro.io/updates',
                    'link_helper' => 'कॉल टू एक्शन के लिए एक गंतव्य URL जोड़ें।',
                    'translations_label' => 'अनुवाद',
                    'translations_helper' => 'शीर्षक, संदेश और बटन कॉपी अनुकूलित करने के लिए किसी भाषा को विस्तार करें।',
                    'fields' => [
                        'title' => 'सूचना शीर्षक',
                        'message' => 'संदेश',
                        'action_label' => 'कॉल टू एक्शन लेबल',
                    ],
                    'submit' => 'सूचना भेजें',
                    'feedback' => [
                        'processing' => 'सूचना भेजी जा रही है…',
                        'success' => 'सूचना हर डैशबोर्ड पर पहुँच गई।',
                        'error' => 'सूचना नहीं भेजी जा सकी।',
                        'missing' => 'कम से कम एक भाषा के लिए शीर्षक और संदेश प्रदान करें।',
                        'invalid_link' => 'कोई मान्य URL दर्ज करें (http:// या https:// से शुरू) या लिंक खाली छोड़ दें।',
                    ],
                ],
                'submit' => 'सूचना सेटिंग्स सहेजें',
                'feedback' => [
                    'processing' => 'सूचना सेटिंग्स सहेजी जा रही हैं…',
                    'saved' => 'सूचना सेटिंग्स अपडेट हो गईं।',
                    'error' => 'सूचना सेटिंग्स सहेजी नहीं जा सकीं।',
                ],
            ],
            'newsletter' => [
                'title' => 'न्यूज़लेटर भेजें',
                'description' => 'सभी उपयोगकर्ताओं या किसी फ़िल्टर किए गए खंड को लक्षित ईमेल भेजें।',
                'helper' => 'उपयोगकर्ताओं को उनके रोल, निर्माण तिथि या सत्यापन स्थिति से फ़िल्टर करें।',
                'filters' => [
                    'role_label' => 'रोल द्वारा फ़िल्टर करें',
                    'status_label' => 'स्थिति द्वारा फ़िल्टर करें',
                    'country_label' => 'देश द्वारा फ़िल्टर करें',
                    'language_label' => 'भाषा द्वारा फ़िल्टर करें',
                ],
                'subject_label' => 'ईमेल विषय',
                'message_label' => 'ईमेल सामग्री',
                'submit' => 'न्यूज़लेटर भेजें',
                'feedback' => [
                    'processing' => 'न्यूज़लेटर भेजा जा रहा है…',
                    'success' => 'न्यूज़लेटर कतार में जोड़ दिया गया है।',
                    'error' => 'न्यूज़लेटर भेजा नहीं जा सका।',
                ],
            ],
            'distribution' => [
                'title' => 'वितरण सेटिंग्स',
                'description' => 'कलाकार डैशबोर्ड को आपके वितरण प्लेटफ़ॉर्म से जोड़ने के लिए Sonosuite विवरण कॉन्फ़िगर करें।',
                'provider_label' => 'वितरण डैशबोर्ड',
                'provider_helper' => 'वर्तमान में Sonosuite समर्थित है।',
                'base_url_label' => 'Sonosuite प्लेटफ़ॉर्म URL',
                'base_url_helper' => 'उदाहरण: https://yourlabel.sonosuite.com',
                'shared_secret_label' => 'Sonosuite SSO साझा सीक्रेट',
                'shared_secret_helper' => 'SSO टोकन उत्पन्न करने के लिए उपयोग किया जाता है।',
                'submit' => 'वितरण सेटिंग्स सहेजें',
                'feedback' => [
                    'processing' => 'वितरण सेटिंग्स सहेजी जा रही हैं…',
                    'saved' => 'वितरण सेटिंग्स अपडेट हो गईं।',
                    'error' => 'वितरण सेटिंग्स सहेजी नहीं जा सकीं।',
                ],
            ],
            'users' => [
                'title' => 'उपयोगकर्ता प्रबंधन',
                'description' => 'सदस्यों को खोजें, उनकी स्थिति अपडेट करें और प्रशासनिक क्रियाएँ करें।',
                'search_placeholder' => 'नाम, ईमेल या आईडी द्वारा खोजें…',
                'filters_toggle' => 'फ़िल्टर दिखाएँ',
                'filters' => [
                    'role' => 'रोल',
                    'status' => 'स्थिति',
                    'country' => 'देश',
                    'language' => 'भाषा',
                    'created_from' => 'निर्मित दिनांक से',
                    'created_to' => 'निर्मित दिनांक तक',
                ],
                'table' => [
                    'name' => 'नाम',
                    'email' => 'ईमेल',
                    'role' => 'रोल',
                    'country' => 'देश',
                    'status' => 'स्थिति',
                    'created_at' => 'बनाया गया',
                    'actions' => 'क्रियाएँ',
                ],
                'actions' => [
                    'view' => 'प्रोफ़ाइल देखें',
                    'impersonate' => 'उपयोगकर्ता के रूप में साइन इन करें',
                    'block' => 'खाता अवरोधित करें',
                    'unblock' => 'खाता पुनर्स्थापित करें',
                    'delete' => 'खाता हटाएँ',
                    'resend_verification' => 'सत्यापन ईमेल पुनः भेजें',
                ],
                'empty' => 'कोई उपयोगकर्ता नहीं मिला। अपने फ़िल्टर समायोजित करें।',
            ],
        ],
        'checkout' => [
            'success' => 'भुगतान की पुष्टि हो गई। पूर्ण रॉयल्टी संग्रह में आपका स्वागत है!',
            'success_plan' => 'भुगतान की पुष्टि हो गई। :plan योजना सक्रिय — पूर्ण रॉयल्टी संग्रह में आपका स्वागत है!',
            'cancel' => 'चेकआउट रद्द किया गया। आपका प्रीमियम अपग्रेड सक्रिय नहीं किया गया।',
        ],
        'footer' => '© :year :site. अपने जज़्बात को बढ़ाएं, कागज़ी काम नहीं।',
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
        'title' => 'MusicDistro.io – एआई के साथ अपना संगीत वितरित करें',
        'meta' => [
            'description' => 'MusicDistro.io एक एआई-संचालित डिजिटल वितरण प्लेटफ़ॉर्म है जो कलाकारों, लेबल और प्रबंधकों को हर रिलीज़ को वैश्विक सफलता में बदलने में मदद करता है। अपने ट्रैक्स जारी करें, अपनी मार्केटिंग अभियानों को सिंक करें और अपनी 100% रॉयल्टी रखें।',
            'keywords' => [
                'डिजिटल संगीत वितरण',
                'एआई संगीत वितरक',
                'संगीत वितरण सेवा',
                'कलाकार स्ट्रीमिंग प्लेटफ़ॉर्म',
                'स्वचालित संगीत मार्केटिंग',
                'musicdistro.io',
            ],
            'og_title' => 'MusicDistro.io – साहसी कलाकारों के लिए एआई संगीत वितरण',
            'og_description' => 'अपने ट्रैक्स रिलीज़ करें, अपनी मार्केटिंग अभियानों को सिंक करें और MusicDistro.io के साथ अपनी रॉयल्टी रखें।',
            'twitter_title' => 'MusicDistro.io – साहसी कलाकारों के लिए एआई संगीत वितरण',
            'twitter_description' => 'अपने ट्रैक्स रिलीज़ करें, अपनी मार्केटिंग अभियानों को सिंक करें और MusicDistro.io के साथ अपनी रॉयल्टी रखें।',
            'structured' => [
                'service_type' => 'एआई द्वारा संचालित डिजिटल संगीत वितरण',
                'area_served' => 'विश्वव्यापी',
                'offers' => [
                    [
                        '@type' => 'Offer',
                        'name' => 'विश्वव्यापी वितरण',
                        'price' => '0.00',
                        'priceCurrency' => 'EUR',
                        'availability' => 'https://schema.org/InStock',
                    ],
                    [
                        '@type' => 'Offer',
                        'name' => 'एआई मार्केटिंग अभियान',
                        'price' => '29.00',
                        'priceCurrency' => 'EUR',
                        'availability' => 'https://schema.org/PreOrder',
                    ],
                ],
            ],
        ],
        'nav' => [
            'brand_aria' => 'MusicDistro परिचय पर वापस जाएँ',
            'toggle_open' => 'नेविगेशन मेनू खोलें',
            'toggle_close' => 'नेविगेशन मेनू बंद करें',
            'menu_heading' => [
                'badge' => 'नेविगेशन',
                'title' => 'MusicDistro.io इकोसिस्टम का अन्वेषण करें',
                'description' => 'वितरण, मार्केटिंग और विश्लेषण टूल आपकी अगली रिलीज़ का समर्थन करने के लिए।',
            ],
            'links' => [
                'mission' => 'हमारा मिशन',
                'features' => 'विशेषताएँ',
                'ai' => 'MusicPulse एआई',
                'faq' => 'प्रश्नोत्तर',
            ],
            'cta' => [
                'register' => 'खाता बनाएं',
                'login' => 'साइन इन करें',
                'dashboard' => 'डैशबोर्ड',
            ],
            'meta' => [
                'availability' => '24/7 उपलब्ध',
                'contact' => 'contact@musicdistro.io',
            ],
        ],
        'hero' => [
            'eyebrow' => 'पहला एआई-संचालित संगीत वितरण',
            'typewriter_phrases' => [
                'अपने संगीत को हर प्लेटफ़ॉर्म पर रिलीज़ करें।',
                'अपने प्रशंसकों को जोड़ें और हर स्ट्रीम इकट्ठा करें।',
                'रिकॉर्ड लेबल से भी बेहतर, एआई आपकी उड़ान संचालित करे!',
            ],
            'subtitle' => 'MusicDistro.io बिना रुकावट वाला वैश्विक वितरण और वृद्धि बुद्धिमत्ता को जोड़ता है ताकि हर रिलीज़ समन्वित लॉन्च बने। स्टूडियो से प्लेलिस्ट तक, सब कुछ समकालिक रहता है ताकि आपका दर्शक बढ़ सके।',
            'cta' => [
                'primary' => 'निःशुल्क शुरू करें',
                'secondary' => 'साइन इन करें',
            ],
            'card' => [
                'aria_label' => 'पार्टनर वितरण प्लेटफ़ॉर्म',
                'badge' => 'DSP हब',
                'title' => 'आपके ट्रैक्स हर प्लेटफ़ॉर्म पर',
                'subtitle' => 'हमारी एआई-संचालित पाइपलाइन के साथ अपने रिलीज़ को प्रमुख DSP और सोशल नेटवर्क पर सिंक करें।',
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
                    'description' => 'पार्टनर प्लेटफ़ॉर्म: Spotify, Apple Music, TikTok, YouTube, Deezer, Amazon Music और अधिक।',
                ],
                [
                    'value' => '0%',
                    'description' => 'आपकी रॉयल्टी का 0% रोका जाता है। आप अपने मास्टर्स के मालिक हैं और वृद्धि नियंत्रित करते हैं।',
                ],
                [
                    'value' => '48h',
                    'description' => '48 घंटों में प्राथमिक रिलीज़ सत्यापन, हमारे एआई-सहायता प्राप्त गुणवत्ता पाइपलाइन की बदौलत।',
                ],
            ],
        ],
        'features' => [
            'title' => 'नेतृत्व चाहने वाले कलाकारों और लेबल के लिए संपूर्ण सेवा',
            'items' => [
                [
                    'title' => 'तुरंत वैश्विक वितरण',
                    'description' => 'एक क्लिक में अपने ट्रैक्स 250+ प्रीमियम प्लेटफ़ॉर्म पर भेजें। एल्गोरिदम आपके मेटाडेटा की जाँच करता है और अनुपालन सुनिश्चित करता है।',
                    'bullets' => [
                        'ड्रैग-एंड-ड्रॉप के साथ बल्क अपलोड।',
                        'क्षेत्रों और रिलीज़ विंडो पर सूक्ष्म नियंत्रण।',
                        'आवश्यक होने पर स्वचालित ISRC/UPC जनरेशन।',
                    ],
                ],
                [
                    'title' => 'कस्टम एआई अभियान',
                    'description' => 'हमारे भाषा मॉडल आपके प्लेलिस्ट पिच, सोशल कॉपी, वीडियो स्क्रिप्ट और फैन ईमेल तैयार करते हैं। हर अभियान आपकी कम्युनिटी को बढ़ाने के लिए कैलिब्रेटेड है।',
                    'bullets' => [
                        'प्रेरणाओं और रुझानों का वास्तविक समय विश्लेषण।',
                        'आपकी शैली से मेल खाते प्लेलिस्ट और मीडिया सुझाव।',
                        'फैन रिएक्शन से ट्रिगर होने वाले स्वचालित फ़ॉलो-अप।',
                    ],
                ],
                [
                    'title' => 'पारदर्शी, रियल-टाइम रॉयल्टी',
                    'description' => 'अपने राजस्व को क्षेत्र, प्लेटफ़ॉर्म और ट्रैक के अनुसार ट्रैक करें। स्ट्रीम बढ़ने पर अलर्ट प्राप्त करें और उत्तर में मार्केटिंग क्रियाएँ ट्रिगर करें।',
                    'bullets' => [
                        'सुरक्षित SSO एक्सेस के साथ एकीकृत डैशबोर्ड।',
                        'कई मुद्राओं और प्राप्तकर्ताओं में तेज़ भुगतान।',
                        'लेखा निर्यात और साझेदार-तैयार रिपोर्ट्स।',
                    ],
                ],
            ],
        ],
        'ai' => [
            'title' => 'MusicPulse एआई: तेज़ी का आपका कमांड सेंटर',
            'items' => [
                [
                    'title' => 'रिलीज़ स्क्रिप्टिंग',
                    'description' => 'MusicPulse आपकी प्रेरणाओं, सामाजिक वार्तालापों और खोज शिखरों का विश्लेषण करता है ताकि सर्वोत्तम लॉन्च क्षण तय हो सके।',
                ],
                [
                    'title' => 'ट्विन फ़ैन्स',
                    'description' => 'अपने वर्तमान प्रशंसकों जैसे दर्शकों की पहचान करें और सहयोग, प्लेलिस्ट व मीडिया प्लेसमेंट सुझाव प्राप्त करें।',
                ],
                [
                    'title' => 'मोमेंटम अलर्ट',
                    'description' => 'जब कोई प्रमुख प्लेलिस्ट आपका ट्रैक जोड़ती है, कोई क्षेत्र तेज़ी पकड़ता है या UGC वायरल होता है तो तुरंत सूचित हों।',
                ],
            ],
        ],
        'testimonials' => [
            'title' => 'उन्होंने अपनी रिलीज़ को दृश्यमान जीत में बदला',
            'prev' => 'पिछला प्रशंसापत्र',
            'next' => 'अगला प्रशंसापत्र',
            'dots_aria' => 'प्रशंसापत्र पृष्ठांकन',
            'dot_label' => 'प्रशंसापत्र :number दिखाएँ',
            'items' => [
                [
                    'quote' => '“MusicDistro.io की बदौलत मैंने तीन महीनों में अपने दर्शकों को 60% बढ़ाया। एआई सिफारिशों ने सही स्थानीय प्लेलिस्ट को लक्ष्य बनाया।”',
                    'author' => 'NAOMI LYS – आर एंड बी गायिका',
                ],
                [
                    'quote' => '“लोग बातें करते हैं, लेकिन MusicDistro.io के एआई ने सब बदल दिया और मेरे करियर को तेज़ कर दिया। हर रिलीज़ स्क्रिप्टेड है और पहले दिन से ही मेरे स्ट्रीम विस्फोट कर जाते हैं।”',
                    'author' => 'DJ NOVEL – इलेक्ट्रॉनिक निर्माता',
                ],
                [
                    'quote' => '“हमारे पुराने वितरक से स्विच करने पर हमारे प्री-सेव तीन गुना हो गए। MusicDistro.io की स्वचालित अनुक्रम रिलीज़ तक प्रशंसकों को उत्साहित रखते हैं।”',
                    'author' => 'BLACKWAVE COLLECTIVE – इंडी लेबल',
                ],
                [
                    'quote' => '“MusicPulse द्वारा जनरेट की गई प्लेलिस्ट पिचों ने हमें पहली ही हफ़्ते में पाँच संपादकीय प्लेलिस्ट पर पहुँचा दिया—अतिरिक्त पीआर हायर किए बिना।”',
                    'author' => 'LINA ORTEGA – मैनेजर',
                ],
                [
                    'quote' => '“रियल-टाइम डैशबोर्ड मुझे हर स्पॉन्सरशिप अभियान को दिन ख़त्म होने से पहले समायोजित करने देता है। हम पिछले साल की तुलना में दोगुने प्री-ऑर्डर कन्वर्ट करते हैं।”',
                    'author' => 'MANELI CRUZ – कलाकार मैनेजर',
                ],
                [
                    'quote' => '“हमारे एआई TikTok अभियान 40% अधिक UGC कंटेंट उत्पन्न करते हैं। MusicDistro.io ने तीन मार्केटिंग टूल की जगह ले ली जिन्हें हम अलग से भुगतान करते थे।”',
                    'author' => 'PULSEWAVE RECORDS – स्वतंत्र लेबल',
                ],
                [
                    'quote' => '“MusicDistro.io ने 48 घंटों में बहुभाषी स्टोरीटेलिंग के साथ एक विश्वव्यापी सरप्राइज़ ड्रॉप आयोजित किया। हमारी ग्लोबल कम्युनिटी तुरंत सक्रिय हो गई।”',
                    'author' => 'AYA NOVA – इंडी पॉप कलाकार',
                ],
                [
                    'quote' => '“प्रतिस्पर्धी विश्लेषण मॉड्यूल ने हमें सही फीचर्स चुनने में मदद की और Spotify तथा Deezer पर हमारी मासिक प्ले तीन गुना हो गई।”',
                    'author' => 'ORION ATLAS – अल्टरनेटिव पॉप डुओ',
                ],
                [
                    'quote' => '“मोमेंटम अलर्ट मुझे उसी मिनट सूचित करते हैं जब कोई प्रमुख प्लेलिस्ट मेरा ट्रैक जोड़ती है। मैं एक घंटे के भीतर प्रशंसकों को पुनः सक्रिय कर लेता हूँ और उत्साह बनाए रखता हूँ।”',
                    'author' => 'KEZZA – रैपर',
                ],
                [
                    'quote' => '“एआई सिफारिशों के साथ हमने दो अंतरराष्ट्रीय बाज़ार खोले और छह महीनों में अपना ऑनलाइन मर्च राजस्व दोगुना कर लिया।”',
                    'author' => 'STELLAR SOUND – बुटीक लेबल',
                ],
            ],
        ],
        'faq' => [
            'title' => 'अक्सर पूछे जाने वाले प्रश्न',
            'entries' => [
                [
                    'question' => 'MusicDistro.io क्या है?',
                    'answer' => 'MusicDistro.io एक एआई-संचालित डिजिटल संगीत वितरण प्लेटफ़ॉर्म है। यह आपके ट्रैक्स को 250 से अधिक प्लेटफ़ॉर्म पर पहुँचाता है, आपकी मार्केटिंग अभियानों को स्वचालित करता है और रॉयल्टी को वास्तविक समय में ट्रैक करता है।',
                ],
                [
                    'question' => 'MusicPulse AI मेरी रिलीज़ रणनीति में कैसे मदद करता है?',
                    'answer' => 'MusicPulse AI आपके दर्शकों और ट्रेंड्स का विश्लेषण करता है ताकि प्लेलिस्ट पिच, सोशल कॉपी, वीडियो स्क्रिप्ट और फैन ईमेल तैयार किए जा सकें—हर अभियान आपके लक्ष्यों के अनुरूप।',
                ],
                [
                    'question' => 'क्या मैं MusicDistro.io पर अपनी रॉयल्टी का 100% रखता हूँ?',
                    'answer' => 'हाँ। आप अपने राजस्व पर पूरा नियंत्रण रखते हैं और त्वरित भुगतान, अलर्ट और विस्तृत रिपोर्ट प्राप्त करते हैं।',
                ],
                [
                    'question' => 'क्या मैं अपना मौजूदा कैटलॉग आयात कर सकता हूँ?',
                    'answer' => 'बिल्कुल। MusicDistro.io CSV आयात, पार्टनर इंटीग्रेशन और API सिंक का समर्थन करता है ताकि आप अपने ISRC/UPC और स्ट्रीमिंग इतिहास को बनाए रखें।',
                ],
                [
                    'question' => 'आप लेबल और मैनेजर को किस तरह का समर्थन देते हैं?',
                    'answer' => 'वितरण के अलावा हम बहु-कलाकार रिपोर्टिंग, ट्रेंड अलर्ट, स्पॉन्सर्ड अधिग्रहण अभियान और पेशेवरों के लिए समर्पित बहुभाषी सहायता प्रदान करते हैं।',
                ],
            ],
        ],
        'cta' => [
            'title' => 'चार्ट में नेतृत्व करने के लिए तैयार हैं?',
            'body' => 'MusicDistro.io से जुड़ें, मिनटों में अपना खाता सक्रिय करें और एआई को आपके संगीत को प्रतीक्षारत प्रशंसकों तक पहुँचने दें।',
            'primary' => 'मेरा खाता बनाएं',
            'secondary' => 'मेरा कार्यक्षेत्र खोलें',
            'image_alt' => 'वैश्विक चार्ट पर चढ़ाई का चित्रण',
        ],
        'footer' => '© :year :site – एआई वितरण जो आपके दर्शकों को बढ़ाता है।',
    ],

    'tutorial' => [
        'title' => 'ट्यूटोरियल: अपना संगीत वितरित करें – :site',
        'header' => [
            'title' => 'विश्वास के साथ अपना संगीत वितरित करें',
            'subtitle' => 'यह ट्यूटोरियल अंतिम मिक्स से लेकर प्लेटफ़ॉर्म अनुमोदन तक आपका मार्गदर्शन करता है। हर चरण का पालन करें ताकि आपकी रिलीज़ उसी भावनात्मक प्रभाव के साथ प्रकाशित हो सके जिसकी आपके दर्शक को उम्मीद है।',
        ],
        'steps' => [
            [
                'title' => 'अपने मास्टर और विज़ुअल तैयार करें',
                'description' => 'अपनी 24-बिट / 48 kHz WAV फ़ाइलें तैयार रखें, 3000x3000 px में कवर निर्यात करें और सुनिश्चित करें कि आपका ID3 मेटाडेटा सुसंगत है।',
                'bullets' => [
                    'कलाकारों और फीचर्ड मेहमानों की वर्तनी जांचें।',
                    'लॉसलेस निर्यात (MP3 नहीं) का उपयोग करें।',
                    'यदि आप गीतों को सिंक करना चाहते हैं तो अपने लिरिक्स तैयार करें।',
                ],
            ],
            [
                'title' => 'Sonosuite में अपनी रिलीज़ बनाएं',
                'description' => 'समर्पित डैशबोर्ड कार्ड के माध्यम से साइन इन करें। Sonosuite में “Create album” पर क्लिक करें और सहायक को आपका मार्गदर्शन करने दें।',
                'bullets' => [
                    'रिलीज़ प्रकार चुनें: सिंगल, ईपी या एल्बम।',
                    'अपना लेबल, गीतकार/संगीतकार और कॉपीराइट वर्ष भरें।',
                    'यदि आपके पास ISRC/UPC कोड हैं तो उन्हें जोड़ते हुए अपने ट्रैक्स अपलोड करें।',
                ],
            ],
            [
                'title' => 'अपनी लॉन्च रणनीति का अनुकूलन करें',
                'description' => 'Spotify for Artists की अनुशंसाओं का लाभ उठाने के लिए रिलीज़ की तारीख कम से कम 10 दिन पहले शेड्यूल करें। एक अनुकूलित मीडिया योजना के लिए हमारे एआई इनसाइट्स सक्रिय करें।',
                'bullets' => [
                    'TikTok, Instagram Reels और YouTube Shorts टीज़र शेड्यूल करें।',
                    'स्वचालित फैन ईमेल और प्लेलिस्ट पिचों की योजना बनाएं।',
                    'यदि आप विशिष्ट बाज़ारों को लक्ष्य बनाते हैं तो विशिष्ट क्षेत्रों को जोड़ें।',
                ],
            ],
            [
                'title' => 'अपने प्रदर्शन को ट्रैक करें',
                'description' => 'जैसे ही रिलीज़ ऑनलाइन होती है, स्ट्रीम, डाउनलोड और रॉयल्टी की निगरानी के लिए Sonosuite पर लौटें। जब आपके गीत गति पकड़ते हैं तो हमारा एआई इंजन आपको सूचित करेगा।',
                'bullets' => [
                    'उन प्लेलिस्ट का विश्लेषण करें जो सबसे अधिक स्ट्रीम उत्पन्न करती हैं।',
                    'उन देशों की पहचान करें जहां प्रशंसक सबसे पहले जुड़ते हैं।',
                    'दर्शक समानता के आधार पर सहयोग की सिफारिशें अनलॉक करें।',
                ],
            ],
        ],
        'cta' => [
            'label' => 'डैशबोर्ड पर लौटें',
            'guest_label' => 'खाता बनाएं',
        ],
    ],
    'validation' => [
        'method_not_allowed' => 'विधि की अनुमति नहीं है।',
        'json_invalid' => 'अमान्य अनुरोध: JSON डेटा पहचाना नहीं गया।',
        'auth_required' => 'प्रमाणीकरण आवश्यक है।',
        'first_name_required' => 'आपका पहला नाम आवश्यक है।',
        'last_name_required' => 'आपका अंतिम नाम आवश्यक है।',
        'email_invalid' => 'कृपया एक मान्य ईमेल पता प्रदान करें।',
        'email_required' => 'आपका ईमेल पता आवश्यक है।',
        'email_exists' => 'इस ईमेल पते से पहले से ही एक खाता मौजूद है। कृपया साइन इन करें या नया सत्यापन लिंक मांगें।',
        'email_unverified' => 'साइन इन करने से पहले अपना ईमेल पुष्टि करें। अपना इनबॉक्स देखें या सहायता से संपर्क करें।',
        'country_required' => 'कृपया कोई देश चुनें।',
        'country_invalid' => 'कृपया मान्य देश चुनें।',
        'role_required' => 'कृपया अपनी व्यावसायिक प्रोफ़ाइल चुनें।',
        'business_type_invalid' => 'कृपया मान्य खाते का प्रकार चुनें।',
        'phone_invalid' => 'मान्य फोन नंबर दर्ज करें (अंकों, रिक्त स्थान और चिह्न .()+- की अनुमति है)।',
        'company_name_required' => 'अपनी कंपनी का नाम दर्ज करें।',
        'password_required' => 'आपका पासवर्ड आवश्यक है।',
        'password_min' => 'पासवर्ड में कम से कम 8 वर्ण होने चाहिए।',
        'password_confirmation' => 'पासवर्ड मेल नहीं खा रहे हैं।',
        'language_invalid' => 'कृपया सूची से एक भाषा चुनें।',
        'credentials_invalid' => 'अमान्य विवरण। कृपया पुनः प्रयास करें या खाता बनाएं।',
        'token_invalid' => 'यह टोकन अवैध है।',
        'avatar_storage_failed' => 'प्रोफ़ाइल चित्र स्टोरेज तैयार नहीं किया जा सका।',
        'avatar_upload_failed' => 'चित्र अपलोड विफल रहा।',
        'avatar_size' => 'प्रोफ़ाइल चित्र 5 एमबी से अधिक नहीं होना चाहिए।',
        'avatar_format' => 'अनुमत प्रारूप: JPEG, PNG या WebP।',
        'avatar_save_failed' => 'प्रोफ़ाइल चित्र सहेजा नहीं जा सका।',
        'user_not_found' => 'उपयोगकर्ता नहीं मिला।',
        'action_missing' => 'क्रिया या उपयोगकर्ता पहचानकर्ता उपलब्ध नहीं है।',
        'cannot_self_manage' => 'आप अपने स्वयं के खाते पर यह कार्रवाई नहीं कर सकते।',
        'cannot_manage_super_admin' => 'यह कार्रवाई सुपर एडमिन पर अनुमत नहीं है।',
        'account_deleted' => 'खाता हटाया जा चुका है।',
        'account_blocked' => 'उपयोगकर्ता की पहुंच अवरुद्ध की गई।',
        'account_already_blocked' => 'खाता पहले से अवरुद्ध है।',
        'account_unblocked' => 'उपयोगकर्ता की पहुंच पुनर्स्थापित की गई।',
        'account_already_active' => 'खाता पहले से सक्रिय है।',
        'unknown_action' => 'अज्ञात क्रिया।',
        'distribution_provider_invalid' => 'कृपया मान्य वितरण डैशबोर्ड चुनें।',
        'sonosuite_base_url_invalid' => 'मान्य Sonosuite प्लेटफ़ॉर्म URL दर्ज करें (https://)।',
        'sonosuite_shared_secret_required' => 'Sonosuite SSO साझा सीक्रेट दर्ज करें।',
    ],
]);
