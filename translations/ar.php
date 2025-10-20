<?php
$base = require __DIR__ . '/en.php';

return array_replace_recursive($base, array (
  'language' =>
  array (
    'label' => 'اللغة',
    'menu_label' => 'تغيير اللغة',
    'choose_language' => 'اختر لغتك',
    'updated' => 'تم تحديث تفضيل اللغة لديك (:language).',
  ),
  'alerts' =>
  array (
    'blocked_access' => 'تم تقييد وصولك.',
  ),
  'email' =>
  array (
    'common' =>
    array (
      'greeting' => 'مرحبًا :name،',
      'greeting_generic' => 'مرحبًا،',
      'signature' => 'نراك قريبًا\\nفريق :site',
    ),
    'verification' =>
    array (
      'subject' => 'أكد بريدك الإلكتروني لـ :site',
      'intro' => 'مرحبًا بك في :site! لتفعيل حسابك والبدء في توزيع موسيقاك، استخدم الرابط أدناه.',
      'action' => 'رابط التحقق: :link',
      'footer' => 'إذا لم تطلب هذا التسجيل، يمكنك تجاهل هذا البريد الإلكتروني.',
    ),
    'reset' =>
    array (
      'subject' => 'أعد تعيين كلمة مرورك على :site',
      'intro' => 'لقد طلبت إعادة تعيين كلمة المرور على :site.',
      'action' => 'لاختيار كلمة مرور جديدة، افتح الرابط التالي: :link',
      'expiration' => 'سينتهي مفعول هذا الرابط خلال 60 دقيقة. إذا لم تطلبه، يمكنك تجاهل هذه الرسالة بأمان.',
    ),
  ),
  'auth' =>
  array (
    'roles' =>
    array (
      'musician' => 'موسيقي',
      'artist' => 'فنان',
      'manager' => 'مدير أعمال',
      'producer' => 'منتج',
      'publisher' => 'ناشر',
      'label' => 'شركة إنتاج',
      'other' => 'أخرى',
      'member' => 'عضو',
    ),
    'common' =>
    array (
      'first_name_label' => 'الاسم الأول',
      'last_name_label' => 'اسم العائلة',
      'email_label' => 'عنوان البريد الإلكتروني',
      'country_label' => 'بلد الإقامة',
      'role_label' => 'ملفك المهني',
      'language_label' => 'اللغة المفضلة',
      'password_label' => 'كلمة المرور',
      'confirm_password_label' => 'تأكيد كلمة المرور',
    ),
    'login' =>
    array (
      'title' => 'تسجيل الدخول',
      'lead' => 'عد إلى لوحة التحكم واستمر في بناء أسطورتك.',
      'submit' => 'تسجيل الدخول',
      'forgot' => 'هل نسيت كلمة المرور؟',
      'register_prompt' => 'ليس لديك حساب بعد؟ :link.',
      'register_link' => 'إنشاء حساب',
    ),
    'register' =>
    array (
      'intro_title' => 'انضم إلى :site ودع الذكاء الاصطناعي يضخم صوتك.',
      'intro_text' => 'نربط كل فنان بالجمهور الذي ينتظر موسيقاه بالفعل. سجّل لتطلق أول إصدار لك خلال دقائق.',
      'bullets' =>
      array (
        'native_ai' => 'تسويق مدعوم بالذكاء الاصطناعي لصياغة حملاتك، وعروض قوائم التشغيل، وتحليلات جمهورك.',
        'worldwide' => 'توزيع عالمي فوري على أكثر من 250 منصة مميزة.',
        'royalties' => 'احتفظ بـ 100٪ من عوائدك مع تتبع شفاف وتنبيهات فورية.',
      ),
      'title' => 'أنشئ حسابك',
      'lead' => 'شارك معلوماتك لتلقي رابط التأكيد.',
      'language_help' => 'سنخصص لوحة التحكم ورسائل البريد الإلكتروني والإرشادات بهذه اللغة.',
      'submit' => 'فعّل حسابي',
      'login_prompt' => 'هل أنت عضو بالفعل؟ :link.',
      'login_link' => 'تسجيل الدخول',
      'success' => 'شكرًا لك! تفقد بريدك الوارد لتأكيد العنوان وتفعيل حسابك.',
    ),
    'forgot' =>
    array (
      'title' => 'هل نسيت كلمة المرور؟',
      'lead' => 'أدخل عنوان بريدك الإلكتروني لتستلم تعليمات إعادة التعيين.',
      'submit' => 'إرسال رابط إعادة التعيين',
      'back_to_login' => 'العودة إلى تسجيل الدخول',
      'success' => 'إذا كان هناك حساب مرتبط بهذا البريد الإلكتروني، فقد أرسلنا لك الآن تعليمات إعادة تعيين كلمة المرور.',
    ),
    'reset' =>
    array (
      'title' => 'اختر كلمة مرور جديدة',
      'lead' => 'اختر كلمة مرور قوية لتأمين حسابك من جديد.',
      'submit' => 'تحديث كلمة المرور',
      'token_invalid' => 'رابط إعادة التعيين هذا غير صالح أو غير مكتمل. اطلب رابطًا جديدًا.',
      'token_expired' => 'انتهت صلاحية رابط إعادة التعيين هذا. اطلب رابطًا جديدًا.',
      'token_used' => 'رابط إعادة التعيين هذا لم يعد صالحًا. اطلب رابطًا جديدًا.',
      'success' => 'تم تحديث كلمة مرورك. يمكنك الآن تسجيل الدخول.',
      'new_password_label' => 'كلمة المرور الجديدة',
      'confirm_password_label' => 'تأكيد كلمة المرور',
      'request_new_link' => 'طلب رابط جديد',
      'return_to_login' => 'العودة إلى تسجيل الدخول',
    ),
    'verify' =>
    array (
      'expired_title' => 'رابط منتهٍ أو غير صالح',
      'expired_body' => 'رابط التحقق الذي استخدمته لم يعد صالحًا. تواصل مع دعمنا على :email للحصول على رابط جديد.',
      'cta_login' => 'العودة إلى تسجيل الدخول',
      'success' => 'تم تأكيد عنوان بريدك الإلكتروني. يمكنك الآن تسجيل الدخول.',
    ),
    'blocked' =>
    array (
      'title' => 'وصول مقيّد',
      'lead' => 'تحتاج مساعدة؟ راسلنا على :email وسيساعدك فريقنا سريعًا.',
      'cta_login' => 'العودة إلى تسجيل الدخول',
    ),
    'profile' =>
    array (
      'updated' => 'تم حفظ التعديلات.',
    ),
  ),
  'home' =>
  array (
    'title' => 'MusicDistro.io – وزّع موسيقاك بالذكاء الاصطناعي',
    'meta' =>
    array (
      'description' => 'MusicDistro.io هي المنصة الرقمية المدعومة بالذكاء الاصطناعي التي تساعد الفنانين والعلامات ومديري الأعمال على تحويل كل إصدار إلى نجاح عالمي. أطلق أعمالك، نسّق حملاتك التسويقية واحتفظ بـ 100٪ من عوائدك.',
      'keywords' =>
      array (
        0 => 'توزيع الموسيقى الرقمي',
        1 => 'موزع موسيقي بالذكاء الاصطناعي',
        2 => 'خدمة توزيع موسيقي',
        3 => 'منصة بث للفنانين',
        4 => 'تسويق موسيقي مؤتمت',
        5 => 'musicdistro.io',
      ),
      'og_title' => 'MusicDistro.io – توزيع موسيقي بالذكاء الاصطناعي للفنانين الطموحين',
      'og_description' => 'أطلق إصداراتك، نسّق حملاتك التسويقية واحتفظ بعوائدك مع MusicDistro.io.',
      'twitter_title' => 'MusicDistro.io – توزيع موسيقي بالذكاء الاصطناعي للفنانين الطموحين',
      'twitter_description' => 'أطلق إصداراتك، نسّق حملاتك التسويقية واحتفظ بعوائدك مع MusicDistro.io.',
      'structured' =>
      array (
        'service_type' => 'توزيع موسيقي رقمي مدعوم بالذكاء الاصطناعي',
        'area_served' => 'حول العالم',
        'offers' =>
        array (
          0 =>
          array (
            '@type' => 'Offer',
            'name' => 'توزيع عالمي',
            'price' => '0.00',
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock',
          ),
          1 =>
          array (
            '@type' => 'Offer',
            'name' => 'حملات تسويق بالذكاء الاصطناعي',
            'price' => '29.00',
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/PreOrder',
          ),
        ),
      ),
    ),
    'nav' =>
    array (
      'brand_aria' => 'العودة إلى مقدمة MusicDistro',
      'toggle_open' => 'فتح قائمة التنقل',
      'toggle_close' => 'إغلاق قائمة التنقل',
      'menu_heading' =>
      array (
        'badge' => 'التنقل',
        'title' => 'استكشف منظومة MusicDistro.io',
        'description' => 'أدوات للتوزيع والتسويق والتحليلات لدعم إصداراتك القادمة.',
      ),
      'links' =>
      array (
        'mission' => 'مهمتنا',
        'features' => 'المزايا',
        'ai' => 'MusicPulse AI',
        'faq' => 'الأسئلة الشائعة',
      ),
      'cta' =>
      array (
        'register' => 'إنشاء حساب',
        'login' => 'تسجيل الدخول',
        'dashboard' => 'لوحة التحكم',
      ),
      'meta' =>
      array (
        'availability' => 'متاح 24/7',
        'contact' => 'contact@musicdistro.io',
      ),
    ),
    'hero' =>
    array (
      'eyebrow' => 'أول توزيع موسيقي مدعوم بالذكاء الاصطناعي',
      'typewriter_phrases' =>
      array (
        0 => 'أطلق موسيقاك على كل منصة.',
        1 => 'نشّط معجبيك واجمع كل استماع.',
        2 => 'الذكاء الاصطناعي يقود صعودك أفضل من أي شركة تسجيل!',
      ),
      'subtitle' => 'MusicDistro.io يجمع بين توزيع عالمي بلا احتكاك وذكاء نمو يحوّل كل إصدار إلى إطلاق منسّق. من الاستوديو إلى قوائم التشغيل، كل شيء يظل متزامنًا لتنمية جمهورك.',
      'cta' =>
      array (
        'primary' => 'ابدأ مجانًا',
        'secondary' => 'تسجيل الدخول',
      ),
      'card' =>
      array (
        'aria_label' => 'منصات التوزيع الشريكة',
        'badge' => 'مركز DSP',
        'title' => 'أعمالك على كل منصة',
        'subtitle' => 'زامن إصداراتك عبر المنصات الكبرى والشبكات الاجتماعية من خلال خطّنا المدعوم بالذكاء الاصطناعي.',
        'platforms' =>
        array (
          0 => 'Spotify',
          1 => 'Apple Music',
          2 => 'TikTok',
          3 => 'YouTube Music',
          4 => 'Deezer',
          5 => 'Amazon Music',
        ),
        'marquee' =>
        array (
          0 => 'Spotify',
          1 => 'Apple Music',
          2 => 'TikTok',
          3 => 'YouTube Music',
          4 => 'Amazon Music',
          5 => 'Deezer',
          6 => 'Instagram Reels',
          7 => 'Facebook Music',
        ),
      ),
      'highlights' =>
      array (
        0 =>
        array (
          'value' => '250+',
          'description' => 'منصة شريكة: Spotify وApple Music وTikTok وYouTube وDeezer وAmazon Music والمزيد.',
        ),
        1 =>
        array (
          'value' => '0%',
          'description' => 'من عوائدك نحتفظ بها. أنت تملك أعمالك وتقود نموك.',
        ),
        2 =>
        array (
          'value' => '48h',
          'description' => 'لمراجعة الإصدارات ذات الأولوية بفضل خط الجودة المدعوم بالذكاء الاصطناعي.',
        ),
      ),
    ),
    'features' =>
    array (
      'title' => 'خدمة متكاملة للفنانين والعلامات التي تريد الريادة',
      'items' =>
      array (
        0 =>
        array (
          'title' => 'توزيع عالمي فوري',
          'description' => 'أرسل أعمالك بنقرة واحدة إلى أكثر من 250 منصة مميزة. يتحقق المحرك من بياناتك ويضمن الامتثال.',
          'bullets' =>
          array (
            0 => 'رفع دفعي بالسحب والإفلات',
            1 => 'تحكم دقيق في المناطق ونوافذ الإصدار',
            2 => 'توليد تلقائي لرموز ISRC/UPC عند الحاجة',
          ),
        ),
        1 =>
        array (
          'title' => 'حملات ذكاء اصطناعي مصممة خصيصًا',
          'description' => 'نماذج اللغة لدينا تصيغ عروض قوائم التشغيل، ونسخ الشبكات الاجتماعية، ونصوص الفيديو ورسائل المعجبين. كل حملة مصممة لتنمية مجتمعك.',
          'bullets' =>
          array (
            0 => 'تحليل لحظي للإلهامات والاتجاهات',
            1 => 'اقتراح قوائم تشغيل ووسائل إعلام تناسب أسلوبك',
            2 => 'متابعة تلقائية تُفعّل وفق تفاعل المعجبين',
          ),
        ),
        2 =>
        array (
          'title' => 'عوائد شفافة في الوقت الفعلي',
          'description' => 'تتبع دخلك حسب الإقليم والمنصة والمسار. استقبل التنبيهات عندما يرتفع الاستماع وفعّل إجراءات تسويقية استجابة لذلك.',
          'bullets' =>
          array (
            0 => 'لوحة موحّدة مع وصول آمن عبر تسجيل دخول واحد',
            1 => 'مدفوعات سريعة بعملات ومستلمين متعددين',
            2 => 'تصدير محاسبي وتقارير جاهزة للشركاء',
          ),
        ),
      ),
    ),
    'ai' =>
    array (
      'title' => 'MusicPulse AI: مركز قيادتك للتسريع',
      'items' =>
      array (
        0 =>
        array (
          'title' => 'كتابة سيناريو الإطلاق',
          'description' => 'يحلّل MusicPulse إلهاماتك وأحاديث الشبكات والقمم البحثية ليحدد أفضل لحظة للإطلاق.',
        ),
        1 =>
        array (
          'title' => 'جماهير شبيهة',
          'description' => 'تعرّف على جماهير تشبه معجبيك الحاليين وتلقَّ اقتراحات تعاون وقوائم تشغيل وتغطيات إعلامية.',
        ),
        2 =>
        array (
          'title' => 'تنبيهات الزخم',
          'description' => 'تلقَّ إشعارًا عند إضافة مسارك إلى قائمة رئيسية أو عند تسارع إقليم أو انتشار محتوى UGC.',
        ),
      ),
    ),
    'testimonials' =>
    array (
      'title' => 'حوّلوا إصداراتهم إلى نجاحات ملموسة',
      'prev' => 'الشهادة السابقة',
      'next' => 'الشهادة التالية',
      'dots_aria' => 'ترقيم الشهادات',
      'dot_label' => 'عرض الشهادة :number',
      'items' =>
      array (
        0 =>
        array (
          'quote' => '"بفضل MusicDistro.io نما جمهوري بنسبة 60٪ خلال ثلاثة أشهر مقارنة بالموزّع السابق. استهدفت توصيات الذكاء الاصطناعي قوائم التشغيل المحلية الصحيحة."',
          'author' => 'NAOMI LYS – مغنية آر أند بي',
        ),
        1 =>
        array (
          'quote' => '"الناس يتكلمون، لكن الذكاء الاصطناعي مع MusicDistro.io غيّر كل شيء وسرّع مسيرتي. كل إصدار مخطّط وتتضاعف استماعاتي من اليوم الأول."',
          'author' => 'DJ NOVEL – منتج إلكتروني',
        ),
        2 =>
        array (
          'quote' => '"الانتقال من موزعنا التاريخي ضاعف الحجوزات المسبقة ثلاث مرات. التسلسلات المؤتمتة في MusicDistro.io تُبقي المعجبين على أهبة الحماس حتى لحظة الإصدار."',
          'author' => 'BLACKWAVE COLLECTIVE – شركة مستقلة',
        ),
        3 =>
        array (
          'quote' => '"عروض قوائم التشغيل التي أنشأها MusicPulse وضعتنا على خمس قوائم تحريرية في الأسبوع الأول دون الحاجة إلى توظيف علاقات عامة إضافية."',
          'author' => 'LINA ORTEGA – مديرة أعمال',
        ),
        4 =>
        array (
          'quote' => '"تتيح لي لوحة التحكم بالوقت الفعلي تعديل كل حملة رعاية قبل نهاية اليوم. نحقق ضعف طلبات الشراء المسبقة مقارنة بالعام الماضي."',
          'author' => 'MANELI CRUZ – مديرة فنانين',
        ),
        5 =>
        array (
          'quote' => '"حملات TikTok بالذكاء الاصطناعي تولّد لنا محتوى UGC أكثر بنسبة 40٪. MusicDistro.io استبدل ثلاثة أدوات تسويقية كنا ندفع لها منفصلة."',
          'author' => 'PULSEWAVE RECORDS – شركة مستقلة',
        ),
        6 =>
        array (
          'quote' => '"نفّذ MusicDistro.io إطلاقًا مفاجئًا عالميًا خلال 48 ساعة مع سرد متعدد اللغات. تحرك مجتمعنا العالمي فورًا."',
          'author' => 'AYA NOVA – فنانة بوب مستقلة',
        ),
        7 =>
        array (
          'quote' => '"ساعدنا قسم التحليل التنافسي في اختيار التعاونات المناسبة وتضاعفت مرات التشغيل الشهرية على Spotify وDeezer ثلاث مرات."',
          'author' => 'ORION ATLAS – ثنائي بوب بديل',
        ),
        8 =>
        array (
          'quote' => '"تنبيهات الزخم تخبرني فورًا عند إضافة مساري إلى قائمة رئيسية. أعيد إشعال تفاعل المعجبين خلال ساعة وأحافظ على الحماس بسهولة."',
          'author' => 'KEZZA – مغني راب',
        ),
        9 =>
        array (
          'quote' => '"بفضل توصيات الذكاء الاصطناعي فتحنا سوقين دوليين وضاعفنا عائدات المتجر الإلكتروني خلال ستة أشهر."',
          'author' => 'STELLAR SOUND – علامة بوتيك',
        ),
      ),
    ),
    'faq' =>
    array (
      'title' => 'الأسئلة الشائعة',
      'entries' =>
      array (
        0 =>
        array (
          'question' => 'ما هو MusicDistro.io؟',
          'answer' => 'MusicDistro.io منصة توزيع موسيقي رقمي مدعومة بالذكاء الاصطناعي. ترسل أعمالك إلى أكثر من 250 منصة وتؤتمت حملاتك التسويقية مع تتبع العوائد لحظيًا.',
        ),
        1 =>
        array (
          'question' => 'كيف يعمل الذكاء الاصطناعي في MusicDistro.io؟',
          'answer' => 'يحلّل محركنا بياناتك الوصفية وإلهاماتك والاتجاهات العالمية ليقترح استراتيجيات إطلاق، ويولّد نسخًا ترويجية، ويحدد قوائم التشغيل المستهدفة ويطلق التنبيهات عند تفاعل جمهورك.',
        ),
        2 =>
        array (
          'question' => 'كم يكلف التوزيع عبر MusicDistro.io؟',
          'answer' => 'التسجيل مجاني. نعمل بنظام مشاركة الإيرادات بشفافية، ولا نحتفظ بأي نسبة من عوائدك ونقدم تعزيزات مميزة اختيارية لتسريع الترويج أو فتح تحليلات متقدمة.',
        ),
        3 =>
        array (
          'question' => 'هل يمكنني ربط الكتالوج الحالي؟',
          'answer' => 'نعم. يدعم MusicDistro.io الكتالوجات الحالية عبر استيراد CSV وتكاملات الشركاء ومزامنة API. تحتفظ بمعرّفات ISRC/UPC وسجل الاستماع.',
        ),
        4 =>
        array (
          'question' => 'ما نوع الدعم الذي تقدمه للعلامات والمديرين؟',
          'answer' => 'إضافة إلى التوزيع نوفر أدوات تقارير متعددة الفنانين، وتنبيهات اتجاه، وحملات اكتساب مدفوعة ودعمًا متعدد اللغات للمحترفين.',
        ),
      ),
    ),
    'cta' =>
    array (
      'title' => 'جاهز لتتصدر القوائم؟',
      'body' => 'انضم إلى MusicDistro.io، فعّل حسابك خلال دقائق ودع الذكاء الاصطناعي يدفع موسيقاك إلى الجمهور الذي ينتظرك.',
      'primary' => 'أنشئ حسابي',
      'secondary' => 'أدخل مساحتي',
      'image_alt' => 'رسم يصوّر صعودًا عالميًا على القوائم',
    ),
    'footer' => '© :year :site – توزيع بالذكاء الاصطناعي ينمّي جمهورك.',
  ),
  'dashboard' =>
  array (
    'title' => 'لوحة التحكم – :site',
    'brand_alt' => 'لوحة تحكم :site',
    'profile_panel' =>
    array (
      'title' => 'ملفك الشخصي',
      'helper' => 'حدّث بياناتك وبلدك ولغتك وصورة ملفك للحفاظ على حضور متناسق.',
      'remove_photo' => 'إزالة الصورة',
      'remove_photo_sr' => 'إزالة الصورة',
      'change_photo' => 'تغيير الصورة',
      'preview_alt' => 'معاينة صورة الملف',
      'photo_alt' => 'صورة الملف',
      'close' => 'إغلاق اللوحة',
      'labels' =>
      array (
        'first_name' => 'الاسم الأول',
        'last_name' => 'اسم العائلة',
        'country' => 'البلد',
        'role' => 'الملف المهني',
        'language' => 'اللغة',
        'address_line1' => 'العنوان سطر 1',
        'address_line2' => 'العنوان سطر 2',
        'postal_code' => 'الرمز البريدي',
        'city' => 'المدينة',
        'phone_number' => 'رقم الهاتف',
        'business_type' => 'نوع الحساب',
        'company_name' => 'اسم الشركة',
        'company_vat' => 'ضريبة القيمة المضافة / رقم الضريبة',
      ),
      'language_help' => 'سنخصص لوحة التحكم والإشعارات بهذه اللغة.',
      'business_type_helper' => 'اختر ما إذا كنت فردًا أو تمثل شركة.',
      'business_fields_helper' => 'تظهر تفاصيل الشركة على الفواتير والتقارير.',
      'business_type_options' =>
      array (
        'individual' => 'فرد',
        'company' => 'شركة',
      ),
      'submit' => 'حفظ التغييرات',
      'submit_processing' => 'جارٍ الحفظ…',
      'logout' => 'تسجيل الخروج',
      'feedback' =>
      array (
        'saving' => 'جارٍ الحفظ…',
        'image_optimizing' => 'جارٍ تحسين الصورة…',
        'image_ready' => 'الصورة جاهزة للحفظ.',
        'image_error' => 'حدث خطأ أثناء معالجة الصورة.',
        'photo_removed' => 'تمت إزالة الصورة. لا تنس الحفظ.',
        'profile_refresh' => 'تم تحديث الملف. جارٍ التحديث…',
        'profile_success' => 'تم تحديث ملفك بنجاح.',
        'profile_error' => 'تعذر تحديث ملفك.',
        'unexpected_error' => 'حدث خطأ غير متوقع. حاول مرة أخرى.',
      ),
    ),
    'welcome' =>
    array (
      'title' => 'مرحبًا :name، جاهز لهزّ العالم؟',
      'body' => 'تجمع مساحتك كل الأدوات لتحويل أعمالك إلى تجارب منتشرة. حضّر الإصدارات، نسّق التوزيع وتابع تفاعل المعجبين لحظة بلحظة.',
    ),
    'studio_card' =>
    array (
      'aria_label' => 'تشغيل MusicDistro Studio',
      'badge' => 'جديد',
      'title' => 'أنشئ وامزج وادمج من المتصفح',
      'subtitle' => 'افتح الاستوديو الاحترافي مع الخط الزمني والمكسر والإضافات والتصدير المباشر.',
      'cta' => 'افتح الاستوديو',
    ),
    'cards' =>
    array (
      'distribution' =>
      array (
        'title' => 'توزيع الموسيقى',
        'description' => 'أطلق إصدارات جديدة، أنشئ ألبومات وراقب العوائد من وحدة التوزيع المتصلة.',
        'link_label' => 'فتح وحدة التوزيع',
      ),
      'tutorial' =>
      array (
        'title' => 'دليل التوزيع خطوة بخطوة',
        'description' => 'اتبع دليلنا المرئي لتحضير الملفات وضبط البيانات الوصفية وتعظيم أثر يوم الإصدار.',
        'link_label' => 'عرض الدليل',
      ),
      'royalties' =>
      array (
        'title' => 'اجمع 100٪ من عوائدك',
        'description' => 'افتح المدفوعات المميزة عبر Spotify وApple Music وYouTube وAmazon Music وأكثر.',
        'link_label' => 'اكتشف الخطة المميزة',
      ),
      'mastering' =>
      array (
        'title' => 'ماسترينغ بالذكاء الاصطناعي',
        'description' => 'احصل على صوت جاهز للبث في دقائق مع الإعدادات الاحترافية والتصدير الفوري.',
        'link_label' => 'تشغيل استوديو الماسترنغ',
      ),
      'smartlinks' =>
      array (
        'title' => 'روابط ذكية لتحويل المعجبين',
        'description' => 'أنشئ صفحات هبوط توجه المعجبين لكل منصة بث في ثوانٍ.',
        'link_label' => 'إدارة الروابط الذكية',
      ),
      'ai_composer' =>
      array (
        'title' => 'AI music composer',
        'description' => 'Draft lyrics, audition Suno-inspired vocalists and render AI instrumentals from your creative brief.',
        'link_label' => 'Launch the AI composer',
        'type' => 'modal',
        'modal_target' => 'aiComposerModal',
        'variant' => 'highlight',
      ),
      'cloud_storage' =>
      array (
        'title' => 'سحابة Musicdistro للفنانين',
        'description' => 'خزّن، وثّق وشارك الماسترز في خزنة مشفّرة مع إثبات ملكية عبر البلوك تشين.',
        'link_label' => 'اكتشف سحابة Musicdistro',
      ),
      'youtube' =>
      array (
        'title' => 'إدارة حقوق YouTube',
        'description' => 'زامن Content ID على YouTube ووحّد قنوات الفنان الرسمية لتجميع الأصول في مكان واحد.',
        'link_label' => 'ضبط إعدادات YouTube',
      ),
      'publishing_administration' =>
      array (
        'title' => 'إدارة النشر',
        'description' => 'سجّل المؤلفات، اجمع الحقوق المجاورة وراقب توزيعات العوائد عالميًا بالزمن الحقيقي.',
        'link_label' => 'إدارة النشر',
      ),
      'royalties_withdrawal' =>
      array (
        'title' => 'سحب العوائد',
        'description' => 'ابدأ عمليات الدفع، اختر الحسابات البنكية وتابع عمليات السحب لكل الكتالوجات من مكان واحد.',
        'link_label' => 'سحب العوائد',
      ),
      'coaching' =>
      array (
        'title' => 'التدريب والحملات بالذكاء الاصطناعي',
        'description' => 'فعّل حملات مدعومة بالذكاء الاصطناعي، حدّد قوائم التشغيل المتوافقة وتلقَّ خطة ترويج شخصية.',
        'link_label' => 'قريبًا',
        'alert' => 'حملات الذكاء الاصطناعي قادمة قريبًا.',
      ),
      'payments' =>
      array (
        'title' => 'الفوترة والمدفوعات',
        'description' => 'راجع المدفوعات، حمّل الفواتير وراقب التجديدات القادمة.',
        'link_label' => 'عرض سجل المدفوعات',
      ),
    ),
    'admin' =>
    array (
      'title' => 'الإدارة',
      'subtitle' => 'راجع كل حساب. هذه الرؤى متاحة فقط للمشرفين الفائقين.',
      'tabs' =>
      array (
        'users' => 'المستخدمون',
        'payments' => 'تحقيق الدخل',
        'notifications' => 'الإشعارات',
        'newsletter' => 'النشرة البريدية',
        'configuration' => 'الإعدادات',
        'distribution' => 'التوزيع',
      ),
      'stripe' =>
      array (
        'title' => 'تهيئة Stripe',
        'description' => 'أدخل مفاتيح Stripe API لتفعيل الاشتراكات المميزة والتجديدات التلقائية.',
        'secret_label' => 'المفتاح السري لـ Stripe',
        'publishable_label' => 'المفتاح القابل للنشر لـ Stripe (اختياري)',
        'helper' => 'نخزن المفاتيح مشفرة في قاعدة بياناتك الخاصة فقط. يمكنك تحديثها في أي وقت.',
        'submit' => 'حفظ إعدادات Stripe',
        'feedback' =>
        array (
          'processing' => 'جارٍ حفظ إعدادات Stripe…',
          'saved' => 'تم تحديث إعدادات Stripe بنجاح.',
          'error' => 'تعذر حفظ إعدادات Stripe. تحقّق من المفاتيح ثم حاول مرة أخرى.',
        ),
      ),
      'monetization' =>
      array (
        'title' => 'تحقيق الدخل من الماسترنغ',
        'description' => 'تحكم في كيفية وصول الفنانين إلى الاستوديو وما إذا كان يجب المرور بعملية دفع عبر Stripe.',
        'currency_section_title' => 'عملات الفوترة',
        'currency_section_description' => 'قم بتهيئة العملات المتاحة عبر جميع الخدمات المدفوعة.',
        'payments_label' => 'فرض رسوم على تنزيلات الماسترنغ',
        'payments_enabled' => 'ستُعرض بطاقات دفع Stripe قبل أن يتمكن المستخدمون من تنزيل الماستر.',
        'payments_disabled' => 'يمكن للمستخدمين تصدير الماستر فورًا دون أي دفعة.',
        'single_label' => 'سعر الماستر الواحد',
        'single_hint' => 'يظهر على بطاقة الماسترنغ الفردية (يورو).',
        'yearly_label' => 'سعر الباقة السنوية للماسترنغ',
        'yearly_hint' => 'المبلغ الإجمالي المفوتر مرة في السنة (يورو).',
        'publishing_title' => 'Publishing administration',
        'publishing_description' => 'Set the one-off onboarding fee for publishing administration.',
        'publishing_price_label' => 'Publishing setup price',
        'publishing_price_helper' => 'Displayed on the publishing administration card (default currency).',
        'currency_suffix' => 'EUR',
        'invalid_price' => 'أدخل سعر ماستر صالحًا (مثل 9.99).',
        'invalid_publishing_price' => 'Enter a valid publishing setup price (e.g. 75).',
      ),
      'cloud_storage' =>
      array (
        'title' => 'سحابة Musicdistro',
        'description' => 'حدد الأسعار لخدمة السحابة الآمنة للفنانين.',
        'usage' =>
        array (
          'title' => 'الفوترة حسب الاستخدام',
          'description' => 'فرض رسوم لكل ميغابايت مخزَّن ولكل ملف مرفوع.',
          'toggle_label' => 'تفعيل الفوترة حسب الاستخدام',
          'price_mb_label' => 'السعر لكل ميغابايت',
          'price_mb_helper' => 'يُفوتر بناءً على متوسط التخزين الشهري.',
          'price_file_label' => 'السعر لكل ملف',
          'price_file_helper' => 'يُفرض مرة واحدة لكل رفع بغض النظر عن الحجم.',
          'state_enabled' => 'تم تفعيل الفوترة حسب الاستخدام',
          'state_disabled' => 'تم تعطيل الفوترة حسب الاستخدام',
        ),
        'subscription' =>
        array (
          'title' => 'عرض الاشتراك',
          'description' => 'قدّم باقة شهرية تتضمن سعة تخزين.',
          'toggle_label' => 'تفعيل عرض الاشتراك',
          'price_label' => 'سعر الاشتراك',
          'price_helper' => 'المبلغ المتكرر المفروض على الفنان.',
          'storage_label' => 'حصة التخزين المشمولة',
          'storage_helper' => 'حدد عدد الميغابايت المتضمنة في الخطة.',
          'storage_suffix' => 'ميغابايت',
          'state_enabled' => 'تم تفعيل الاشتراك',
          'state_disabled' => 'تم تعطيل الاشتراك',
        ),
        'validation_usage' => 'وفّر سعرًا لكل ميغابايت ولكل ملف لتفعيل الفوترة حسب الاستخدام.',
        'validation_subscription' => 'حدد سعر الاشتراك وحصة التخزين لتفعيل العرض.',
      ),
      'notifications' =>
      array (
        'title' => 'إشعارات لوحة التحكم',
        'description' => 'تحكم في الإشعارات التي تظهر في لوحة الفنان.',
        'display_label' => 'عرض أيقونة الإشعارات',
        'display_helper' => 'إخفاء أيقونة الجرس من اللوحة عند التعطيل.',
        'automations_title' => 'التنبيهات المؤتمتة',
        'automations_description' => 'اختر التذكيرات المؤتمتة التي ستُرسل للمستخدمين.',
        'profile_incomplete_label' => 'تذكير المستخدمين باستكمال بيانات الملف الشخصي',
        'profile_incomplete_helper' => 'يُرسل تذكيرًا عند غياب العنوان أو البلد أو رقم الهاتف.',
        'broadcast' =>
        array (
          'title' => 'بث مخصّص',
          'description' => 'أرسل إشعارًا مخصصًا إلى كل لوحة فنان.',
          'helper' => 'ترجم الرسالة لكل لغة. الحقول الفارغة ترث النسخة الإنجليزية.',
          'link_label' => 'رابط الإشعار (اختياري)',
          'link_placeholder' => 'https://musicdistro.io/updates',
          'link_helper' => 'أضف رابطًا للانتقال مع الدعوة إلى الإجراء.',
          'translations_label' => 'الترجمات',
          'translations_helper' => 'وسّع لغة لضبط العنوان والنص وزر الدعوة إلى الإجراء.',
          'fields' =>
          array (
            'title' => 'عنوان الإشعار',
            'message' => 'النص',
            'action_label' => 'نص زر الدعوة إلى الإجراء',
          ),
          'submit' => 'إرسال الإشعار',
          'feedback' =>
          array (
            'processing' => 'جارٍ إرسال الإشعار…',
            'success' => 'تم تسليم الإشعار إلى كل لوحة.',
            'error' => 'تعذر إرسال الإشعار.',
            'missing' => 'وفّر على الأقل عنوانًا ورسالة للغة واحدة.',
            'invalid_link' => 'أدخل رابطًا صالحًا (يبدأ بـ http:// أو https://) أو اترك الحقل فارغًا.',
          ),
        ),
        'submit' => 'حفظ إعدادات الإشعارات',
        'feedback' =>
        array (
          'saved' => 'تم تحديث إعدادات الإشعارات.',
          'error' => 'تعذر حفظ إعدادات الإشعارات.',
          'processing' => 'جارٍ الحفظ…',
        ),
      ),
      'newsletter' =>
      array (
        'title' => 'حملات النشرة البريدية',
        'description' => 'صغ وارسِل حملات بريد إلكتروني إلى مستخدميك.',
        'sender' =>
        array (
          'title' => 'المرسل',
          'name_label' => 'اسم المرسل',
          'email_label' => 'بريد المرسل',
          'reply_to_label' => 'عنوان الرد (اختياري)',
          'reply_to_placeholder' => 'support@musicdistro.io',
          'helper' => 'يُرسل كل بريد بشكل منفصل بهذه الهوية.',
        ),
        'recipients' =>
        array (
          'title' => 'المستلمون',
          'helper' => 'اختر من يجب أن يستلم الحملة.',
          'mode_all' => 'كل المستخدمين',
          'mode_selected' => 'اختيار مستلمين',
          'select_label' => 'اختيار المستخدمين',
          'select_helper' => 'اضغط مع الاستمرار على Ctrl (ويندوز) أو Command (ماك) لاختيار عدة مستخدمين.',
          'empty' => 'لا يوجد مستلمون متاحون.',
          'additional_label' => 'عناوين بريد إضافية',
          'additional_placeholder' => 'artist@example.com, manager@example.com',
        ),
        'content' =>
        array (
          'title' => 'المحتوى',
          'subject_label' => 'موضوع البريد',
          'subject_placeholder' => 'أعلن عن أحدث تحديثاتك',
          'html_label' => 'محتوى HTML',
          'html_placeholder' => '<h1>هل أنت مستعد لإطلاقك التالي؟</h1>',
          'helper' => 'استخدم HTML لتصميم بريد غني. يُستخدم النص العادي كبديل.',
          'text_label' => 'نسخة نصية عادية',
          'text_placeholder' => 'مرحبًا! إليك ما هو جديد…',
        ),
        'delivery' =>
        array (
          'title' => 'التسليم',
          'transport_label' => 'الإرسال عبر',
          'transport_options' =>
          array (
            'phpmail' => 'دالة PHP mail()',
            'smtp' => 'خادم SMTP مخصص',
          ),
          'batch_label' => 'رسائل في كل دفعة',
          'interval_label' => 'الفاصل بين الدفعات',
          'interval_helper' => 'تجنّب حدود المزود عبر توزيع عمليات الإرسال.',
          'interval_unit' =>
          array (
            'seconds' => 'ثوانٍ',
            'minutes' => 'دقائق',
            'hours' => 'ساعات',
          ),
          'smtp' =>
          array (
            'host_label' => 'مضيف SMTP',
            'port_label' => 'المنفذ',
            'encryption_label' => 'التشفير',
            'encryption_none' => 'بدون',
            'encryption_ssl' => 'SSL/TLS',
            'encryption_tls' => 'STARTTLS',
            'username_label' => 'اسم المستخدم',
            'password_label' => 'كلمة المرور',
          ),
        ),
        'submit' => 'إرسال النشرة',
        'feedback' =>
        array (
          'processing' => 'جارٍ إرسال النشرة…',
          'success' => 'تم إرسال النشرة إلى :count مستلم.',
          'partial' => 'أُرسلت النشرة إلى :sent مستلمًا. فشلت :failed عمليات تسليم.',
          'error' => 'تعذر إرسال النشرة.',
        ),
        'validation' =>
        array (
          'subject' => 'أدخل موضوعًا للنشرة.',
          'html' => 'وفّر محتوى HTML للنشرة.',
          'recipients' => 'اختر مستلمًا واحدًا على الأقل أو وفّر عناوين بريد.',
          'sender_email' => 'أدخل بريدًا إلكترونيًا صالحًا للمرسل.',
          'reply_to' => 'أدخل عنوان رد صالح.',
          'smtp_host' => 'وفّر مضيف SMTP.',
          'smtp_port' => 'أدخل منفذ SMTP صالحًا.',
        ),
      ),
      'configuration' =>
      array (
        'title' => 'تهيئة لوحة التحكم',
        'description' => 'فعّل أو أخفِ الوحدات المتاحة للفنانين.',
        'studio' =>
        array (
          'title' => 'بطاقة الاستوديو',
          'description' => 'تحكم في ظهور بطاقة الترويج لاستوديو الموسيقى.',
          'toggle_label' => 'عرض بطاقة استوديو الموسيقى',
        ),
        'cards' =>
        array (
          'title' => 'بطاقات الاختصارات',
          'description' => 'اختر بطاقات الاختصار التي تظهر في لوحة التحكم.',
          'toggle_label' => 'عرض البطاقة ":card"',
          'empty' => 'لا توجد بطاقات متاحة حاليًا.',
        ),
        'submit' => 'حفظ الإعدادات',
        'feedback' =>
        array (
          'processing' => 'جارٍ حفظ الإعدادات…',
          'saved' => 'تم تحديث إعدادات لوحة التحكم.',
          'error' => 'تعذر حفظ الإعدادات. حاول مرة أخرى.',
        ),
      ),
      'distribution' =>
      array (
        'title' => 'تهيئة التوزيع',
        'description' => 'اختر لوحة التحكم التي تدير سير عمل التوزيع لديك.',
        'provider_label' => 'لوحة التوزيع',
        'providers' =>
        array (
          'sonosuite' => 'لوحة Sonosuite الداخلية',
        ),
        'fields' =>
        array (
          'sonosuite' =>
          array (
            'base_url_label' => 'رابط منصة Sonosuite',
            'base_url_placeholder' => 'https://platform.musicdistribution.cloud',
            'shared_secret_label' => 'السر المشترك لـ SSO',
            'helper' => 'يُستخدم لبدء جلسات الدخول الموحد مع منصة Sonosuite.',
          ),
        ),
        'submit' => 'حفظ إعدادات التوزيع',
        'feedback' =>
        array (
          'processing' => 'جارٍ حفظ إعدادات التوزيع…',
          'saved' => 'تم تحديث إعدادات التوزيع بنجاح.',
          'error' => 'تعذر حفظ إعدادات التوزيع. حاول مرة أخرى.',
        ),
      ),
      'table' =>
      array (
        'headers' =>
        array (
          'id' => 'المعرف',
          'name' => 'الاسم الكامل',
          'email' => 'البريد الإلكتروني',
          'country' => 'الدولة',
          'created_at' => 'تاريخ الإنشاء',
          'last_login_at' => 'آخر تسجيل دخول',
          'last_login_ip' => 'آخر عنوان IP',
          'status' => 'الحالة',
          'actions' => 'الإجراءات',
        ),
        'empty' => 'لم يتم تسجيل أي حساب بعد.',
        'status' =>
        array (
          'verified' => 'مؤكد',
          'pending' => 'قيد الانتظار',
          'blocked' => 'محظور',
          'super_admin' => 'مشرف فائق',
        ),
        'actions' =>
        array (
          'block' => 'حظر الوصول',
          'unblock' => 'استعادة الوصول',
          'impersonate' => 'تسجيل الدخول كالمستخدم',
          'delete' => 'حذف',
          'self' => 'لا يمكن تنفيذ الإجراء على حسابك',
        ),
        'confirm' =>
        array (
          'delete' => 'تأكيد حذف هذا الحساب؟',
          'block' => 'حظر وصول هذا المستخدم؟',
          'unblock' => 'استعادة وصول هذا المستخدم؟',
          'impersonate' => 'تسجيل الدخول بهذا المستخدم؟',
        ),
        'feedback' =>
        array (
          'processing' => 'جارٍ التنفيذ…',
          'success' => 'اكتمل الإجراء بنجاح.',
          'error' => 'تعذر إكمال الإجراء. حاول مرة أخرى.',
        ),
      ),
      'login_history' =>
      array (
        'badge' => 'الأمان',
        'title' => 'نشاط تسجيل الدخول',
        'subtitle' => 'عمليات الدخول الأخيرة لـ :name',
        'subtitle_generic' => 'عمليات الدخول الأخيرة',
        'button_label' => 'السجل',
        'open_label' => 'عرض سجل تسجيل الدخول',
        'close' => 'إغلاق سجل تسجيل الدخول',
        'back' => 'رجوع',
        'loading' => 'جارٍ استرجاع نشاط تسجيل الدخول…',
        'error' => 'تعذر تحميل نشاط تسجيل الدخول.',
        'empty' => 'لم تُسجل أي عمليات دخول بعد.',
        'footer' => 'يُخزَّن عنوان IP والأجهزة لحماية حسابك.',
        'current_badge' => 'الأحدث',
        'device_summary' => ':device • :os • :browser',
        'device_summary_fallback' => 'معلومات الجهاز غير متاحة',
        'device_unknown' => 'الجهاز',
        'os_unknown' => 'النظام',
        'browser_unknown' => 'المتصفح',
        'user_agent_label' => 'معرّف المستخدم',
        'time_label' => 'تم تسجيل الدخول في :value',
        'unknown_ip' => 'عنوان IP غير معروف',
      ),
      'user_modal' =>
      array (
        'badge' => 'الملف الشخصي',
        'title' => 'تفاصيل المستخدم',
        'subtitle' => 'أُنشئ الحساب في :date',
        'close' => 'إغلاق تفاصيل المستخدم',
        'helper' => 'المعلومات التي جُمعت أثناء التسجيل وتحديثات الملف.',
        'open' => 'عرض التفاصيل',
      ),
    ),
    'notifications' =>
    array (
      'panel' =>
      array (
        'title' => 'الإشعارات',
        'empty' => 'لقد اطلعت على كل شيء.',
        'toggle' => 'فتح الإشعارات',
        'close' => 'إغلاق لوحة الإشعارات',
      ),
      'actions' =>
      array (
        'open_profile' => 'استكمال ملفي الشخصي',
        'open_link' => 'فتح الرابط',
      ),
      'items' =>
      array (
        'profile_incomplete' =>
        array (
          'title' => 'أكمل معلومات ملفك',
          'message' => 'أضف العنوان والبلد ورقم الهاتف لإتمام التسجيل.',
        ),
      ),
    ),
    'cloud_modal' =>
    array (
      'badge' => 'جديد',
      'title' => 'سحابة Musicdistro للفنانين',
      'subtitle' => 'حافظ على كل ميكس وعقد وستيم في مساحة آمنة مع إثبات ملكية عبر البلوك تشين.',
      'features_title' => 'لماذا يحب الفنانون سحابتنا',
      'features' =>
      array (
        0 =>
        array (
          'title' => 'إثبات ملكية عبر البلوك تشين',
          'description' => 'يُختم كل رفع بطابع زمني وتجزئة موثقة لتثبت التأليف فورًا.',
        ),
        1 =>
        array (
          'title' => 'خزنة إبداعية متينة',
          'description' => 'خزّن الماسترز، الستيمات، الأعمال الفنية والعقود مع تشفير مكرر وتنظيم ذكي.',
        ),
        2 =>
        array (
          'title' => 'تعاون بتحكم كامل',
          'description' => 'شارك روابط آمنة بصلاحيات دقيقة، الغِ الوصول بنقرة وتابع النشاط.',
        ),
      ),
      'highlights_title' => 'ماذا ستحصل',
      'highlights' =>
      array (
        0 => 'تتبع الإصدارات وسجل تفصيلي لكل أصل.',
        1 => 'تنبيهات فورية عند عرض الملفات أو تنزيلها.',
        2 => 'وصول عبر الويب والهاتف ليبقى أرشيفك معك دائمًا.',
      ),
      'pricing' =>
      array (
        'title' => 'أسعار شفافة',
        'usage' =>
        array (
          'label' => 'فوترة حسب الاستخدام',
          'value' => ':price_mb لكل ميغابايت مخزَّن + :price_file لكل رفع',
        ),
        'subscription' =>
        array (
          'label' => 'خطة اشتراك',
          'value' => ':price شهريًا مع :storage :unit مشمولة',
          'storage_unit' => 'ميغابايت',
        ),
      ),
      'cta_label' => 'الوصول إلى الخدمة',
      'opt_out_label' => 'عدم إظهار هذه المقدمة مرة أخرى',
      'disclaimer' => 'يمكنك فتح سحابة Musicdistro في أي وقت من لوحة التحكم.',
      'actions' =>
      array (
        'close' => 'إغلاق',
      ),
    ),
    'royalties_modal' =>
    array (
      'headline' => 'طوّر حسابك لتحافظ على 100٪ من عوائدك',
      'subheadline' => 'أوقف مشاركة 30٪ من إيراداتك. عزّز كل إصدار بعوائد مميزة، دعم شخصي وتحليلات دقيقة.',
      'switch' =>
      array (
        'label' => 'اختر وتيرة الدفع',
        'monthly' => 'شهري',
        'yearly' => 'سنوي',
      ),
      'plans' =>
      array (
        'monthly' =>
        array (
          'label' => 'شهري',
          'tagline' => 'مرونة كاملة – يمكنك الإلغاء متى شئت.',
          'price_main' => '9',
          'price_decimal' => '.99',
          'frequency' => '/شهري',
          'note' => 'عضوية تُجدّد تلقائيًا. يمكنك الإلغاء في أي وقت.',
          'cta' => 'اختر الخطة الشهرية',
        ),
        'yearly' =>
        array (
          'label' => 'سنوي',
          'tagline' => 'أفضل قيمة – احصل على شهرين مجانًا.',
          'price_main' => '99',
          'price_decimal' => '',
          'frequency' => '/سنة',
          'note' => 'مثالية للإصدارات الجادة – وفّر ما يعادل شهرين.',
          'cta' => 'اختر الخطة السنوية',
        ),
      ),
      'features_title' => 'كل ما تحتاجه للاحتراف',
      'features' =>
      array (
        0 => 'احتفظ بـ 100٪ من عوائدك على Spotify وApple Music وYouTube وAmazon Music وDeezer وأكثر من 150 منصة.',
        1 => 'فواتير تُنشأ تلقائيًا، كشوف جاهزة للضرائب وأمان Stripe في كل تجديد.',
        2 => 'مسار توزيع أولوية، مراقبة صحة الإصدارات وتنبيهات عوائد استباقية.',
        3 => 'تحليلات فورية لبثك وإيراداتك.',
      ),
      'plan_highlights_title' => 'تفاصيل عملية',
      'plan_highlights' =>
      array (
        0 => 'تفعيل فوري بمجرد تأكيد الدفع.',
        1 => 'يمكنك الإلغاء في أي وقت مباشرة من لوحة التحكم.',
        2 => 'نرسل الفواتير إلى بريدك تلقائيًا.',
      ),
      'guarantee' => 'فوترة آمنة عبر Stripe. تسعير شفاف دون رسوم مخفية.',
      'actions' =>
      array (
        'close' => 'إغلاق',
        'processing' => 'جارٍ الاتصال بـ Stripe…',
      ),
      'checkout' =>
      array (
        'generic_error' => 'تعذر بدء عملية الدفع عبر Stripe. حاول مرة أخرى أو تواصل مع الدعم.',
        'missing_key' => 'إعدادات Stripe غير متوفرة. تواصل مع المشرف.',
        'success_redirect' => 'جارٍ التحويل إلى الدفع الآمن…',
      ),
    ),
    'musicdistribution_modal' =>
    array (
      'badge' => 'جديد',
      'title' => 'وحدة MusicDistribution',
      'subtitle' => 'نسّق كل إصدار من مساحة MusicDistribution المخصصة. أطلق المشاريع، ادفع التسليمات وراقب العوائد دون مغادرة اللوحة.',
      'features_title' => 'ما الذي يمكنك القيام به بالداخل',
      'features' =>
      array (
        0 => 'إنشاء سنغلز وEP وألبومات عبر تدفقات بيانات موجهة.',
        1 => 'دفع التسليمات إلى أكثر من 250 منصة وتتبع حالة الإدراج لحظيًا.',
        2 => 'تجميع الكشوف، تتبع السلف وتصدير تقارير العوائد فورًا.',
      ),
      'cta_label' => 'تشغيل MusicDistribution',
      'cta_processing' => 'جارٍ الاتصال بـ MusicDistribution…',
      'cta_error' => 'تم حظر النوافذ المنبثقة. اسمح بها ثم حاول مرة أخرى.',
      'opt_out_label' => 'عدم إظهار هذه المقدمة مرة أخرى',
      'disclaimer' => 'سنفتح MusicDistribution في تبويب جديد باستخدام جلسة دخول آمنة.',
      'actions' =>
      array (
        'close' => 'إغلاق',
      ),
    ),

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
    'mastering_modal' =>
    array (
      'title' => 'استوديو Musicdistro للماسترينغ',
      'description' => 'استفد من محرك الماسترنغ بالذكاء الاصطناعي لتقديم صوت أعلى وأنقى دون مغادرة لوحة التحكم.',
      'dropzone' =>
      array (
        'title' => 'اسحب مسارك هنا',
        'subtitle' => 'ملفات WAV أو AIFF أو MP3 حتى 250 ميغابايت.',
        'button' => 'رفع ملف',
        'hint' => 'أو انقر لاستعراض جهازك.',
      ),
      'analysis' =>
      array (
        'title' => 'وحدة تحكم الماسترنغ بالذكاء الاصطناعي',
      ),
      'status' =>
      array (
        'idle' => 'اسحب الميكس أو اضغط لاختيار ملف.',
        'uploading' => 'جارٍ رفع الصوت…',
        'analyzing' => 'جارٍ تحليل الصوت…',
        'rendering' => 'جارٍ تجهيز الماستر…',
        'ready' => 'الماستر جاهز – استكشف الإعدادات أدناه.',
        'error' => 'حدث خطأ. حاول بملف آخر.',
      ),
      'processing' =>
      array (
        'uploading' => 'جارٍ الرفع إلى وحدة الماسترنغ…',
        'analyzing' => 'جارٍ تحليل الديناميك والصورة الاستيريو…',
        'rendering' => 'جارٍ تلميع الماستر…',
      ),
      'player' =>
      array (
        'title' => 'استمع وقارن',
        'subtitle' => 'اختر إعدادًا واضغط تشغيل لتسمع التحسين.',
        'listen_master' => 'الاستماع للماستر',
        'listen_original' => 'التبديل إلى النسخة الأصلية',
        'duration_placeholder' => '—:—',
      ),
      'presets' =>
      array (
        'legend' => 'كل سلسلة أعدّها فريق الماسترنغ لدينا لسيناريوهات إصدار مختلفة.',
        'categories' =>
        array (
          'reference' => 'مرجعي',
          'impact' => 'تأثير',
          'bounce' => 'ارتداد',
          'spark' => 'شرارة',
          'energy' => 'طاقة',
          'groove' => 'إيقاع',
          'horizon' => 'منصة',
          'analog' => 'تناظري',
          'air' => 'هواء',
          'manual' => 'مخصص',
        ),
        'original' => 'المكس الأصلي',
        'radio' => 'ضربة إذاعية',
        'hiphop' => 'قيادة هيب هوب',
        'electro' => 'شرارة إلكترونية',
        'edm' => 'ماكس EDM',
        'dance' => 'تألّق حلبة الرقص',
        'festival' => 'تأثير المهرجان',
        'warm' => 'شريط دافئ',
        'spatial' => 'هواء فضائي',
        'custom' => 'نحت مخصص',
      ),
      'visualizer' =>
      array (
        'title' => 'مرئي ذكاء اصطناعي آني',
        'subtitle' => 'راقب الماستر عبر شكل موجي حي وتحليلات طاقة طيفية.',
        'waveform' => 'مقياس تذبذب عصبي',
        'spectrum' => 'طاقة توافقية',
      ),
      'controls' =>
      array (
        'title' => 'تحكمات متقدمة',
        'subtitle' => 'شكّل الماستر كما يفعل مهندس إضافات: عدّل الديناميك، النبرة والعرض الاستيريو مباشرة.',
        'pre_gain' => 'مستوى الإدخال',
        'threshold' => 'حد الضاغط',
        'ratio' => 'نسبة الضغط',
        'attack' => 'الهجوم',
        'release' => 'التحرير',
        'low' => 'تعزيز الجهير',
        'mid' => 'نحت الوسط',
        'high' => 'تعزيز الهواء',
        'width' => 'العرض الاستيريو',
        'output' => 'شدة الخرج',
        'mono' => 'مراقبة أحادية',
      ),
      'errors' =>
      array (
        'invalid_type' => 'يرجى رفع ملف WAV أو AIFF أو MP3.',
        'too_large' => 'الملف كبير جدًا. الحد 250 ميغابايت.',
        'load' => 'تعذر قراءة الملف الصوتي. حاول تصديره مجددًا.',
      ),
      'checkout' =>
      array (
        'title' => 'صدّر الماستر الخاص بك',
        'hint' => 'دفع آمن عبر Stripe – إيصال وفاتورة فورية.',
        'single' =>
        array (
          'label' => 'ماستر هذا المسار • :price',
          'description' => 'نزّل ملفات WAV + MP3 والستيمات لهذا العنوان.',
          'product_name' => 'Musicdistro Mastering – مسار واحد',
          'product_description' => 'ماستر بالذكاء الاصطناعي لمرة واحدة مع مهلة مراجعة 7 أيام.',
        ),
        'subscription' =>
        array (
          'label' => 'ماسترينغ غير محدود • :price_month/شهريًا يُدفع سنويًا (:price_year/سنوياً)',
          'description' => 'تصدير غير محدود، معالجة أولوية ومطابقة مرجعية.',
          'product_name' => 'Musicdistro Mastering – اشتراك سنوي غير محدود',
          'product_description' => '12 شهرًا من الماسترنغ غير المحدود لكل إصداراتك.',
        ),
        'processing_label' => 'جارٍ الاتصال بـ Stripe…',
        'success_redirect' => 'جارٍ التحويل إلى Stripe Checkout…',
        'generic_error' => 'تعذر بدء الدفع عبر Stripe. حاول مرة أخرى.',
        'missing_key' => 'لم يتم إعداد Stripe بعد. يرجى التواصل مع الدعم.',
        'success_single' => 'تم تأكيد الدفع! سيصل الماستر الخاص بك قريبًا.',
        'success_yearly' => 'مرحبًا بك في الماسترنغ غير المحدود – تم تفعيل اشتراكك السنوي.',
        'cancel_single' => 'تم إلغاء عملية الدفع. لم يتم خصم أي مبلغ.',
        'cancel_yearly' => 'تم إلغاء اشتراك الدفع. لم يتم خصم أي مبلغ.',
        'disabled' => 'تم تعطيل مدفوعات الماسترنغ من قبل المشرف.',
      ),
      'download' =>
      array (
        'label' => 'تحميل ملف الماستر',
        'description' => 'صدّر ملف WAV النهائي مباشرة من متصفحك — دون أي دفعات.',
        'hint' => 'تصدير فوري للماستر بجودة WAV عالية.',
        'processing' => 'جارٍ تصيير WAV الماستر…',
        'success' => 'الماستر جاهز — سيبدأ التنزيل بعد لحظات.',
        'error' => 'تعذر تصدير ملف الماستر. حاول مرة أخرى.',
        'unsupported' => 'التصدير غير مدعوم في هذا المتصفح. جرّب Chrome أو Edge.',
        'unavailable' => 'أسقط مسارًا ودع الذكاء الاصطناعي ينهي الماسترنغ قبل التصدير.',
      ),
      'actions' =>
      array (
        'new_file' => 'ماستر مسار آخر',
      ),
    ),
    'smartlinks_modal' =>
    array (
      'badge' => 'تجريبي',
      'title' => 'روابط Musicdistro الذكية',
      'description' => 'أنشئ صفحات هبوط متعددة المنصات في ثوانٍ وتابع النقرات، التحويلات والمناطق الجغرافية – بديل Linkfire مدمج في لوحتك.',
      'tabs' =>
      array (
        'create' => 'إنشاء رابط ذكي',
        'analytics' => 'التحليلات والسجل',
      ),
      'form' =>
      array (
        'upc_label' => 'UPC أو معرف الإصدار',
        'upc_placeholder' => 'مثال: 123456789012',
        'slug_label' => 'عنوان URL مخصص (اختياري)',
        'slug_placeholder' => 'my-album-launch',
        'slug_hint' => 'اتركه فارغًا لتوليد رابط نظيف تلقائيًا.',
        'platforms_label' => 'منصات البث المتصلة',
        'platforms_hint' => 'بدّل الخدمات التي ستظهر في صفحة الهبوط.',
        'submit' => 'توليد رابط ذكي',
        'processing' => 'جارٍ إنشاء الرابط الذكي…',
      ),
      'preview' =>
      array (
        'title' => 'معاينة الصفحة',
        'subtitle' => 'شارك الرابط أدناه ليدخل المعجبون إلى منصتهم المفضلة.',
        'share_label' => 'الرابط القابل للمشاركة',
        'copy' => 'نسخ الرابط',
        'copied' => 'تم النسخ!',
        'empty' => 'ستظهر معاينة الرابط الذكي هنا بعد إنشائه.',
        'cta_label' => 'الاستماع على',
      ),
      'success' =>
      array (
        'title' => 'الرابط الذكي جاهز للمشاركة',
        'message' => 'صفحتك جاهزة. انسخ الرابط وتابع الأداء من علامة تبويب التحليلات.',
      ),
      'history' =>
      array (
        'title' => 'أحدث الروابط الذكية',
        'empty' => 'لا توجد روابط بعد. أنشئ أول رابط أعلاه.',
        'created' => 'تم الإنشاء في :date',
        'analytics' => 'عرض التحليلات',
        'delete' => 'حذف',
        'confirm_delete' => 'حذف الرابط الذكي ":name"؟ ستُزال البيانات المخزنة.',
        'deleted' => 'تم حذف الرابط الذكي.',
      ),
      'analytics' =>
      array (
        'title' => 'تحليلات الأداء',
        'empty' => 'أنشئ رابطًا ذكيًا لفتح التحليلات.',
        'selector_label' => 'روابطك الذكية',
        'summary' =>
        array (
          'clicks' => 'إجمالي النقرات',
          'ctr' => 'معدل النقر',
          'conversions' => 'تحويلات الاستماع',
          'saves' => 'الحفظات والطلبات المسبقة',
        ),
        'geo_title' => 'الانتشار العالمي',
        'geo_subtitle' => 'يمثل كل نبض دولة تتفاعل مع رابطك الذكي.',
        'platforms_title' => 'توزيع المنصات',
        'cities_title' => 'أهم المدن',
        'timeline_title' => 'الزمن التفاعلي',
        'recent_title' => 'أحدث اللمحات',
      ),
      'actions' =>
      array (
        'view_analytics' => 'عرض التحليلات',
      ),
      'errors' =>
      array (
        'upc_required' => 'أدخل UPC لإنشاء رابط ذكي.',
        'spotify_failback' => 'لم نعثر بعد على رابط Spotify لهذا UPC. تأكد من الرمز أو انتظر قليلًا قبل إنشاء الرابط الذكي.',
      ),
    ),
  ),
  'home' =>
  array (
    'title' => 'MusicDistro.io – وزع موسيقاك بالذكاء الاصطناعي',
    'meta' =>
    array (
      'description' => 'MusicDistro.io هي منصة توزيع رقمي مدعومة بالذكاء الاصطناعي تساعد الفنانين والشركات على تحويل كل إصدار إلى نجاح عالمي. أطلق مقاطعك، نسّق حملاتك التسويقية واحتفظ بـ 100٪ من عوائدك.',
      'keywords' =>
      array (
        0 => 'توزيع موسيقى رقمي',
        1 => 'موزع موسيقى بالذكاء الاصطناعي',
        2 => 'خدمة توزيع موسيقي',
        3 => 'منصة بث للفنانين',
        4 => 'تسويق موسيقي آلي',
        5 => 'musicdistro.io',
      ),
      'og_title' => 'MusicDistro.io – توزيع موسيقي بالذكاء الاصطناعي للفنانين الجريئين',
      'og_description' => 'أطلق مساراتك، نسّق حملاتك التسويقية واحتفظ بعوائدك مع MusicDistro.io.',
      'twitter_title' => 'MusicDistro.io – توزيع موسيقي بالذكاء الاصطناعي للفنانين الجريئين',
      'twitter_description' => 'أطلق مساراتك، نسّق حملاتك التسويقية واحتفظ بعوائدك مع MusicDistro.io.',
      'structured' =>
      array (
        'service_type' => 'توزيع موسيقي رقمي مدعوم بالذكاء الاصطناعي',
        'area_served' => 'حول العالم',
        'offers' =>
        array (
          0 =>
          array (
            '@type' => 'Offer',
            'name' => 'توزيع عالمي',
            'price' => '0.00',
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/InStock',
          ),
          1 =>
          array (
            '@type' => 'Offer',
            'name' => 'حملات تسويق بالذكاء الاصطناعي',
            'price' => '29.00',
            'priceCurrency' => 'EUR',
            'availability' => 'https://schema.org/PreOrder',
          ),
        ),
      ),
    ),
    'nav' =>
    array (
      'brand_aria' => 'العودة إلى مقدمة MusicDistro',
      'toggle_open' => 'فتح قائمة التنقل',
      'toggle_close' => 'إغلاق قائمة التنقل',
      'menu_heading' =>
      array (
        'badge' => 'التنقل',
        'title' => 'اكتشف منظومة MusicDistro.io',
        'description' => 'أدوات للتوزيع والتسويق والتحليلات لدعم إصداراتك القادمة.',
      ),
      'links' =>
      array (
        'mission' => 'مهمتنا',
        'features' => 'المزايا',
        'ai' => 'MusicPulse AI',
        'faq' => 'الأسئلة الشائعة',
      ),
      'cta' =>
      array (
        'register' => 'إنشاء حساب',
        'login' => 'تسجيل الدخول',
        'dashboard' => 'لوحة التحكم',
      ),
      'meta' =>
      array (
        'availability' => 'متاح 24/7',
        'contact' => 'contact@musicdistro.io',
      ),
    ),
    'hero' =>
    array (
      'eyebrow' => 'أول توزيع موسيقي مدفوع بالذكاء الاصطناعي',
      'typewriter_phrases' =>
      array (
        0 => 'أصدر موسيقاك على كل منصة.',
        1 => 'اجذب معجبيك واجمع كل استماع.',
        2 => 'الذكاء الاصطناعي يقود صعودك أفضل من أي شركة إنتاج!',
      ),
      'subtitle' => 'تجمع MusicDistro.io بين توزيع عالمي سلس وذكاء نمو يحوّل كل إصدار إلى إطلاق منظم. من الاستوديو إلى قوائم التشغيل، يبقى كل شيء متزامنًا لتنمية جمهورك.',
      'cta' =>
      array (
        'primary' => 'ابدأ مجانًا',
        'secondary' => 'تسجيل الدخول',
      ),
      'card' =>
      array (
        'aria_label' => 'منصات التوزيع الشريكة',
        'badge' => 'DSP HUB',
        'title' => 'مساراتك على كل منصة',
        'subtitle' => 'زامن إصداراتك عبر أهم DSP والشبكات الاجتماعية مع خط أنابيبنا المدعوم بالذكاء الاصطناعي.',
        'platforms' =>
        array (
          0 => 'Spotify',
          1 => 'Apple Music',
          2 => 'TikTok',
          3 => 'YouTube Music',
          4 => 'Deezer',
          5 => 'Amazon Music',
        ),
        'marquee' =>
        array (
          0 => 'Spotify',
          1 => 'Apple Music',
          2 => 'TikTok',
          3 => 'YouTube Music',
          4 => 'Amazon Music',
          5 => 'Deezer',
          6 => 'Instagram Reels',
          7 => 'Facebook Music',
        ),
      ),
      'highlights' =>
      array (
        0 =>
        array (
          'value' => '250+',
          'description' => 'منصات شريكة: Spotify، Apple Music، TikTok، YouTube، Deezer، Amazon Music وأكثر.',
        ),
        1 =>
        array (
          'value' => '0%',
          'description' => 'من عوائدك نحتفظ بها. تمتلك الماستر وتوجّه نموك.',
        ),
        2 =>
        array (
          'value' => '48 س',
          'description' => 'للموافقة على الإصدارات ذات الأولوية بفضل خط الجودة المدعوم بالذكاء الاصطناعي.',
        ),
      ),
    ),
    'features' =>
    array (
      'title' => 'خدمة متكاملة صُممت للفنانين والشركات التي تريد القيادة',
      'items' =>
      array (
        0 =>
        array (
          'title' => 'توزيع عالمي فوري',
          'description' => 'أرسل مساراتك بنقرة واحدة إلى أكثر من 250 منصة مميزة. يتحقق النظام من بياناتك الوصفية ويضمن الالتزام.',
          'bullets' =>
          array (
            0 => 'رفع جماعي بالسحب والإفلات',
            1 => 'تحكم دقيق في المناطق ونوافذ الإصدار',
            2 => 'توليد ISRC/UPC تلقائي عند الحاجة',
          ),
        ),
        1 =>
        array (
          'title' => 'حملات ذكاء اصطناعي مصممة خصيصًا',
          'description' => 'تصيغ نماذج اللغة عروض قوائم التشغيل، النصوص الاجتماعية، سيناريوهات الفيديو ورسائل المعجبين. كل حملة مصممة لتنمية مجتمعك.',
          'bullets' =>
          array (
            0 => 'تحليل لحظي للإلهام والاتجاهات',
            1 => 'اقتراحات قوائم تشغيل ووسائط تناسب أسلوبك',
            2 => 'متابعات آلية تُطلق عند تفاعل المعجبين',
          ),
        ),
        2 =>
        array (
          'title' => 'عوائد شفافة في الوقت الفعلي',
          'description' => 'تتبع إيراداتك حسب الإقليم والمنصة والمسار. تلقَّ تنبيهات عند ارتفاع التدفقات وفعّل إجراءات تسويق استجابةً لذلك.',
          'bullets' =>
          array (
            0 => 'لوحة موحدة مع وصول آمن عبر SSO',
            1 => 'مدفوعات سريعة بعملات ومستلمين متعددين',
            2 => 'تصدير محاسبي وتقارير جاهزة للشركاء',
          ),
        ),
      ),
    ),
    'ai' =>
    array (
      'title' => 'MusicPulse AI: مركز التحكم لتسريع مسارك',
      'items' =>
      array (
        0 =>
        array (
          'title' => 'برمجة الإطلاق',
          'description' => 'يحلل MusicPulse مصادر إلهامك، المحادثات الاجتماعية وذروات البحث لتحديد أفضل لحظة لإطلاقك.',
        ),
        1 =>
        array (
          'title' => 'معجبون توأم',
          'description' => 'حدد جماهير مشابهة لمعجبيك الحاليين وتلقَّ اقتراحات تعاون وقوائم تشغيل وتغطية إعلامية.',
        ),
        2 =>
        array (
          'title' => 'تنبيهات الزخم',
          'description' => 'صَلْك إشعار عند إضافة مسارك إلى قائمة رئيسية، أو عند تسارع إقليم ما أو انتشار محتوى UGC.',
        ),
      ),
    ),
    'testimonials' =>
    array (
      'title' => 'حوّلوا إصداراتهم إلى نجاحات ملموسة',
      'prev' => 'الشهادة السابقة',
      'next' => 'الشهادة التالية',
      'dots_aria' => 'ترقيم الشهادات',
      'dot_label' => 'عرض الشهادة :number',
      'items' =>
      array (
        0 =>
        array (
          'quote' => '"بفضل MusicDistro.io زاد جمهوري بنسبة 60٪ في ثلاثة أشهر مقارنة بالموزع السابق. استهدفت توصيات الذكاء الاصطناعي قوائم التشغيل المحلية المناسبة."',
          'author' => 'NAOMI LYS – مغنية R&B',
        ),
        1 =>
        array (
          'quote' => '"الناس يتحدثون، لكن الذكاء الاصطناعي غيّر كل شيء وسرّع مسيرتي مع MusicDistro.io. كل إصدار مخطط وتتفجر تدفقاتي منذ اليوم الأول."',
          'author' => 'DJ NOVEL – منتج إلكتروني',
        ),
        2 =>
        array (
          'quote' => '"الانتقال من موزعنا التاريخي ضاعف الحجوزات المسبقة ثلاث مرات. التسلسلات الآلية في MusicDistro.io تُبقي المعجبين في حالة ترقب حتى لحظة الإطلاق."',
          'author' => 'BLACKWAVE COLLECTIVE – شركة مستقلة',
        ),
        3 =>
        array (
          'quote' => '"عروض قوائم التشغيل التي يولدها MusicPulse وضعتنا على خمس قوائم تحريرية في الأسبوع الأول دون استئجار علاقات عامة إضافية."',
          'author' => 'LINA ORTEGA – مديرة أعمال',
        ),
        4 =>
        array (
          'quote' => '"تتيح لي لوحة المعلومات اللحظية تعديل كل حملة رعاية قبل نهاية اليوم. نضاعف الحجوزات المسبقة مقارنة بالعام الماضي."',
          'author' => 'MANELI CRUZ – مديرة فنانين',
        ),
        5 =>
        array (
          'quote' => '"حملات TikTok بالذكاء الاصطناعي تولد 40٪ محتوى UGC إضافي. MusicDistro.io استبدلت ثلاثة أدوات تسويق كنا ندفع لها منفصلة."',
          'author' => 'PULSEWAVE RECORDS – شركة مستقلة',
        ),
        6 =>
        array (
          'quote' => '"نظّم MusicDistro.io إطلاقًا مفاجئًا عالميًا في 48 ساعة مع سرد متعدد اللغات. تحرك مجتمعنا العالمي فورًا."',
          'author' => 'AYA NOVA – فنانة بوب مستقل',
        ),
        7 =>
        array (
          'quote' => '"ساعدتنا وحدة تحليل المنافسين على اختيار التعاونات المناسبة وتثليث عدد تشغيلاتنا الشهرية على Spotify وDeezer."',
          'author' => 'ORION ATLAS – ثنائي بوب بديل',
        ),
        8 =>
        array (
          'quote' => '"تنبيهات الزخم تُخبرني لحظة إضافة مساري إلى قائمة رئيسية. أعيد إشعال المعجبين خلال ساعة وأحافظ على الضجة بسهولة."',
          'author' => 'KEZZA – مغني راب',
        ),
        9 =>
        array (
          'quote' => '"مع توصيات الذكاء الاصطناعي فتحنا سوقين دوليين وضاعفنا مبيعات المنتجات عبر الإنترنت خلال ستة أشهر."',
          'author' => 'STELLAR SOUND – شركة بوتيك',
        ),
      ),
    ),
    'faq' =>
    array (
      'title' => 'الأسئلة الأكثر طرحًا',
      'entries' =>
      array (
        0 =>
        array (
          'question' => 'ما هي MusicDistro.io؟',
          'answer' => 'MusicDistro.io منصة توزيع موسيقي رقمي مدعومة بالذكاء الاصطناعي. توصل مساراتك إلى أكثر من 250 منصة مع أتمتة الحملات التسويقية وتتبع العوائد في الوقت الفعلي.',
        ),
        1 =>
        array (
          'question' => 'كيف يعمل الذكاء الاصطناعي في MusicDistro.io؟',
          'answer' => 'يحلل محركنا بياناتك الوصفية، مصادر إلهامك والاتجاهات العالمية ليقترح استراتيجيات الإصدار، وينتج نصوصًا ترويجية، ويحدد قوائم تشغيل مستهدفة ويطلق التنبيهات عند تفاعل جمهورك.',
        ),
        2 =>
        array (
          'question' => 'كم يكلف التوزيع عبر MusicDistro.io؟',
          'answer' => 'التسجيل مجاني. نعمل بنظام مشاركة إيرادات شفاف، لا نحتفظ بأي نسبة من عوائدك ونقدم ترقيات مدفوعة اختيارية لتسريع الترويج أو فتح تحليلات متقدمة.',
        ),
        3 =>
        array (
          'question' => 'هل يمكنني ربط الكتالوج الحالي؟',
          'answer' => 'نعم. تدعم MusicDistro.io الكتالوجات الحالية عبر استيراد CSV وتكاملات الشركاء والمزامنة عبر API. تحتفظ بمعرّفات ISRC/UPC وسجل البث.',
        ),
        4 =>
        array (
          'question' => 'ما الدعم الذي تقدمه للشركات والمديرين؟',
          'answer' => 'بالإضافة إلى التوزيع نوفر أدوات تقارير متعددة الفنانين، تنبيهات اتجاهات، حملات استحواذ مدفوعة ودعمًا متعدد اللغات للمتخصصين.',
        ),
      ),
    ),
    'cta' =>
    array (
      'title' => 'جاهز لتتصدر القوائم؟',
      'body' => 'انضم إلى MusicDistro.io، فعّل حسابك خلال دقائق ودع الذكاء الاصطناعي يدفع موسيقاك إلى المعجبين الذين ينتظرونك.',
      'primary' => 'إنشاء حسابي',
      'secondary' => 'الوصول إلى مساحتي',
      'image_alt' => 'رسم بياني لصعود عالمي',
    ),
    'footer' => '© :year :site – التوزيع بالذكاء الاصطناعي الذي ينمي جمهورك.',
  ),
));
