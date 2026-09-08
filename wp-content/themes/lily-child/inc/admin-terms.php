<?php
/**
 * Lily Terms & Conditions dashboard fields.
 *
 * Reuses the existing Lily settings system (same option, same save flow,
 * same admin helpers as homepage/about/faq/contact): the terms_* keys live
 * inside `lily_homepage_settings` and render as a "Terms Page" tab on the
 * existing Lily admin page.
 *
 * Single source of truth: lily_terms_approved_content() holds the complete
 * approved English + Arabic Terms & Conditions (hero, intro and all 29
 * numbered sections) verbatim. The page template uses it as fallback and
 * the initial DB population copies it into the dashboard fields, so the
 * brand owner can edit both languages afterwards without touching code.
 *
 * Rendering preserves the existing Terms page markup and CSS exactly:
 * the same hero, the same numbered rows, the same CTA band. Multi-line
 * bodies keep their line breaks via nl2br inside the existing paragraph
 * element — no new elements, classes, or styles.
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Complete approved Terms & Conditions content (verbatim source).
 *
 * @return array
 */
function lily_terms_approved_content() {
	static $content = null;

	if ( null !== $content ) {
		return $content;
	}

	$content = array(
		'hero_title_en' => 'TERMS & CONDITIONS',
		'hero_title_ar' => 'الشروط والأحكام',
		'hero_sub_en'   => 'Welcome to Lily – Lenses Original Lily.

These Terms & Conditions govern the use of lilylenses.com and the purchase of products offered through the website, and explain the rights and obligations of both the customer and Lily.

Please read these Terms & Conditions carefully before submitting your order.

By completing a purchase order, you acknowledge that you have reviewed these Terms & Conditions, the Privacy Policy, and the Shipping, Exchange and Return Policies available on the website.

Submitting an order through the website alone does not constitute final acceptance by Lily. An order becomes confirmed in accordance with the order confirmation process described below.

Nothing in these Terms & Conditions shall reduce or limit any mandatory rights granted to consumers under Egyptian law.',
		'hero_sub_ar'   => 'مرحبًا بك في ليلي – Lenses Original Lily.

تنظم هذه الشروط والأحكام استخدام موقع lilylenses.com وشراء المنتجات المعروضة من خلاله، وتوضح حقوق والتزامات كل من العميل و«ليلي».

يرجى قراءة هذه الشروط بعناية قبل إرسال طلبك.

بإتمام طلب الشراء، فإنك تقر بأنك اطلعت على الشروط والأحكام وسياسة الخصوصية وسياسات الشحن والاستبدال والاسترجاع المتاحة على الموقع.

ولا يعد إرسال الطلب من الموقع وحده قبولًا نهائيًا من جانب «ليلي»، ويصبح الطلب مؤكدًا وفقًا لما هو موضح في بند الطلبات وتأكيدها أدناه.

ولا يترتب على أي حكم في هذه الشروط الانتقاص من الحقوق التي يمنحها القانون المصري للمستهلك.',
		'sections'      => array(
			array(
				'num'      => '01',
				'title_en' => 'SUPPLIER INFORMATION',
				'body_en'  => 'The website is operated and products are sold under:

Trade Name: Lenses Original Lily – ليلي

Customer Service & WhatsApp: 01060760098

Website: lilylenses.com',
				'title_ar' => 'بيانات المورد',
				'body_ar'  => 'يتم تشغيل الموقع والبيع من خلال:

الاسم التجاري: Lenses Original Lily – ليلي

رقم خدمة العملاء وواتساب: 01060760098

الموقع الإلكتروني: lilylenses.com',
			),
			array(
				'num'      => '02',
				'title_en' => 'PRODUCTS',
				'body_en'  => 'Lily sells contact lenses and products related to their use and care. Products offered may include:

- Coloured contact lenses.
- Contact lenses with different prescription powers.
- Clear contact lenses.
- Contact lens care products.
- Other products displayed on the website from time to time.

We aim to display each product\'s essential information, specifications, images, colours, sizes, prescription powers and usage duration as clearly as reasonably possible.

The appearance of colour may vary slightly from one person to another depending on natural eye colour, lighting, device display settings and photography. A minor difference resulting solely from these factors shall not by itself constitute a product defect.

The information and specifications stated on the product packaging remain the primary reference regarding technical details, expiry date and usage instructions.',
				'title_ar' => 'المنتجات',
				'body_ar'  => 'تبيع «ليلي» عدسات لاصقة ومنتجات مرتبطة باستخدامها والعناية بها، وقد تشمل المنتجات المعروضة:

● العدسات اللاصقة الملونة.
● العدسات اللاصقة بدرجات نظر مختلفة.
● العدسات الشفافة.
● مستلزمات العناية بالعدسات.
● المنتجات الأخرى التي يتم عرضها على الموقع من وقت إلى آخر.

نسعى إلى عرض بيانات كل منتج وصفاته الأساسية وصوره وألوانه ومقاساته ودرجاته ومدة استخدامه بصورة واضحة قدر الإمكان.

وقد تختلف درجة ظهور اللون بصورة محدودة من شخص إلى آخر نتيجة لون العين الطبيعي والإضاءة وإعدادات شاشة الجهاز والتصوير، ولا يعد الاختلاف البسيط الناتج عن هذه العوامل وحده عيبًا في المنتج.

ويظل الوصف والمواصفات المدونة على عبوة المنتج هي المرجع فيما يتعلق ببياناته الفنية ومدة صلاحيته وتعليمات استخدامه.',
			),
			array(
				'num'      => '03',
				'title_en' => 'IMPORTANT NOTICE REGARDING CONTACT LENSES AND EYE HEALTH',
				'body_en'  => 'Contact lenses are products that come into direct contact with the eye and must therefore be used correctly and safely.

Lenses Original Lily is not a medical provider and does not perform eye examinations, diagnose eye conditions, issue prescriptions or determine the appropriate prescription power for customers.

If you choose contact lenses with a particular prescription power, the selection of that power and size is based on the information you provide.

You should consult an ophthalmologist or qualified professional when necessary to determine whether contact lenses are suitable for you and to identify the appropriate prescription and size, particularly if you are using contact lenses for the first time or have an eye-related problem or health condition.

Entering a prescription power on the website does not mean that Lily has medically reviewed or approved it.

Customers must follow:

- The usage instructions provided with the product.
- Cleaning and disinfection instructions.
- The specified lens usage period.
- Proper storage instructions.
- Any instructions provided by an ophthalmologist or qualified professional.

Customers must not:

- Share contact lenses with another person.
- Use contact lenses after their expiry date.
- Exceed the product\'s specified usage period.
- Sleep while wearing lenses unless the product is designed for such use and a qualified professional has advised accordingly.
- Use a damaged or contaminated lens.

If pain, noticeable redness, irritation or an unusual change in vision occurs while using contact lenses, you should stop using them and seek appropriate medical advice.

Nothing in the above provisions exempts Lily or any responsible party from liability imposed by law where a product defect, incorrect product information, improper storage or handling, or any other circumstance giving rise to legal liability exists.',
				'title_ar' => 'تنويه مهم بشأن العدسات وصحة العين',
				'body_ar'  => 'العدسات اللاصقة منتج يتعامل بصورة مباشرة مع العين، ولذلك يجب استخدامها بطريقة صحيحة وآمنة.

Lenses Original Lily ليست جهة طبية، ولا يقوم الموقع بإجراء فحص للنظر أو تشخيص حالات العين أو إصدار وصفة طبية أو تحديد درجة النظر المناسبة للعميل.

إذا اخترت عدسات بدرجة نظر معينة، فإن اختيار الدرجة والمقاس يتم بناءً على البيانات التي تقدمها أنت.

ويجب الرجوع إلى طبيب عيون أو مختص مؤهل عند الحاجة لتحديد مدى ملاءمة العدسات لك والدرجة والمقاس المناسبين، وبصفة خاصة عند استخدام العدسات لأول مرة أو وجود مشكلة أو حالة صحية تتعلق بالعين.

إدخال درجة نظر على الموقع لا يعني أن «ليلي» قامت بمراجعتها أو اعتمادها طبيًا.

ويلتزم العميل باتباع:

● تعليمات الاستخدام الموجودة على المنتج.
● تعليمات التنظيف والتعقيم.
● المدة المحددة لاستخدام العدسة.
● طريقة الحفظ الصحيحة.
● أي تعليمات يقدمها الطبيب أو المختص.

ولا يجوز:

● مشاركة العدسات مع شخص آخر.
● استخدام العدسات بعد انتهاء مدة صلاحيتها.
● تجاوز مدة الاستخدام المحددة للمنتج.
● النوم بالعدسات إلا إذا كان المنتج مصممًا لذلك ووفق توجيه مختص.
● استخدام عدسة تالفة أو ملوثة.

إذا حدث ألم أو احمرار ملحوظ أو تهيج أو تغير غير معتاد في الرؤية أثناء استخدام العدسات، فيجب التوقف عن استخدامها وطلب المشورة الطبية المناسبة.

ولا يعفي أي من الأحكام السابقة «ليلي» أو أي طرف مسؤول من المسؤوليات التي يقررها القانون في حالة وجود عيب في المنتج أو خطأ في بياناته أو حفظه أو تداوله أو أي حالة أخرى تنشأ فيها مسؤولية قانونية.',
			),
			array(
				'num'      => '04',
				'title_en' => 'CHOOSING PRESCRIPTION POWER AND SIZE',
				'body_en'  => 'The customer is responsible for ensuring the accuracy of any information entered or confirmed regarding:

- Prescription power.
- Size.
- Colour.
- Product type.
- Quantity.
- Usage duration.

We do not determine the appropriate prescription power based on a customer\'s description of symptoms or previous eyesight strength.

If you are unsure of the appropriate prescription power or size, we recommend consulting an ophthalmologist or qualified professional before placing an order.

Where a product is prepared according to specifications selected by the customer, the applicable exchange and return rules shall be determined in accordance with the law and the policy published on the website, depending on the nature and condition of the product.',
				'title_ar' => 'اختيار الدرجة والمقاس',
				'body_ar'  => 'يتحمل العميل مسؤولية التأكد من صحة البيانات التي يقوم بإدخالها أو تأكيدها بشأن:

● درجة النظر.
● المقاس.
● اللون.
● نوع المنتج.
● الكمية.
● مدة الاستخدام.

ولا نحدد الدرجة المناسبة للعميل بناءً على وصفه لأعراضه أو قوة نظره السابقة.

وفي حالة عدم معرفة الدرجة أو المقاس المناسب، ننصح بالرجوع إلى طبيب عيون أو مختص مؤهل قبل إتمام الطلب.

وإذا تم تجهيز منتج بناءً على مواصفات خاصة يحددها العميل، فتطبق بشأن الاستبدال أو الاسترجاع القواعد القانونية والسياسة المعلنة على الموقع بحسب طبيعة المنتج والحالة.',
			),
			array(
				'num'      => '05',
				'title_en' => 'PRICES',
				'body_en'  => 'All prices displayed on the website are in Egyptian Pounds unless otherwise stated.

The product price and any additional applicable charges, including delivery fees, will be communicated before the order is confirmed.

Product prices and promotions may change from time to time. A price change shall not affect an order that has already been confirmed at the final agreed price.

If an obvious and unintended material error appears in the price or description of a product, we will contact the customer before confirming the order to explain the correct price or information.

The customer shall not be bound by the corrected price unless they are informed of it and agree to it before the order is confirmed.

Once an order has been confirmed at the final price, that price shall not be changed except where permitted by law or pursuant to a clear agreement with the customer.',
				'title_ar' => 'الأسعار',
				'body_ar'  => 'جميع الأسعار المعروضة على الموقع تكون بالجنيه المصري ما لم يُذكر خلاف ذلك.

يتم توضيح سعر المنتج وأي مصروفات إضافية مستحقة، بما في ذلك مصاريف التوصيل، قبل تأكيد الطلب.

قد تتغير أسعار المنتجات والعروض من وقت إلى آخر، ولا يؤثر تغيير السعر على طلب سبق تأكيده بالسعر النهائي المتفق عليه.

وفي حالة ظهور خطأ مادي واضح وغير مقصود في سعر أو وصف منتج، يتم التواصل مع العميل قبل تأكيد الطلب لتوضيح السعر أو البيانات الصحيحة.

ولا يصبح العميل ملزمًا بالسعر المصحح إلا بعد إبلاغه به وموافقته عليه قبل تأكيد الطلب.

أما بعد تأكيد الطلب بالسعر النهائي، فلا يتم تغييره إلا في الحالات التي يسمح بها القانون أو بناءً على اتفاق واضح مع العميل.',
			),
			array(
				'num'      => '06',
				'title_en' => 'OFFERS AND DISCOUNTS',
				'body_en'  => 'We may offer promotions, discounts or promotional codes from time to time.

Such offers may be subject to specific conditions, including:

- A limited duration.
- Specific products.
- Limited quantities.
- A minimum order value.
- Restrictions on combining multiple offers.

Any special conditions applicable to an offer will be displayed when the offer is announced.

Promotional codes and offers must not be used fraudulently or in violation of their published conditions.',
				'title_ar' => 'العروض والخصومات',
				'body_ar'  => 'قد نقدم عروضًا أو خصومات أو أكوادًا ترويجية من وقت إلى آخر.

وقد تخضع هذه العروض لشروط خاصة مثل:

● مدة محددة.
● منتجات محددة.
● كمية محددة.
● حد أدنى للطلب.
● عدم إمكانية الجمع بين أكثر من عرض.

وتعرض الشروط الخاصة بالعرض وقت الإعلان عنه، إن وجدت.

ولا يجوز استخدام الأكواد أو العروض بطريقة احتيالية أو مخالفة للشروط المعلنة.',
			),
			array(
				'num'      => '07',
				'title_en' => 'PLACING AND CONFIRMING ORDERS',
				'body_en'  => 'Customers may select products and submit purchase orders through the website.

Submitting an order to Lily means that we have received your request to purchase the selected products, but does not necessarily mean that the order has immediately become confirmed and final.

Our team may contact you by telephone or WhatsApp to:

- Verify customer information.
- Review the selected products and quantities.
- Review the prescription power or size entered by the customer.
- Confirm the delivery address.
- Clarify delivery fees.
- Confirm the final price.
- Determine or estimate the delivery date.

An order becomes confirmed after our applicable confirmation procedures have been completed and the customer has been informed accordingly.

In all cases, customers retain the legal rights granted in relation to distance contracts, correcting or amending orders, and withdrawing from a contract where the relevant legal conditions apply.',
				'title_ar' => 'إرسال الطلب وتأكيده',
				'body_ar'  => 'يستطيع العميل اختيار المنتجات وإرسال طلب الشراء من خلال الموقع.

إرسال الطلب إلى «ليلي» يعني أننا استلمنا طلبًا منك لشراء المنتجات المختارة، ولكنه لا يعني بالضرورة أن الطلب أصبح مؤكدًا ونهائيًا في اللحظة نفسها.

قد يتواصل معك فريقنا عبر الهاتف أو واتساب من أجل:

● التحقق من بيانات العميل.
● مراجعة المنتجات والكمية.
● مراجعة الدرجة أو المقاس الذي أدخله العميل.
● تأكيد عنوان التوصيل.
● توضيح مصاريف التوصيل.
● تأكيد السعر النهائي.
● تحديد أو تقدير موعد التوصيل.

يصبح الطلب مؤكدًا بعد إتمام إجراءات التأكيد المتبعة لدينا وإبلاغ العميل بذلك.

ويتمتع العميل، في جميع الأحوال، بالحقوق المقررة قانونًا بشأن التعاقد عن بُعد وتصحيح أو تعديل الطلب والعدول عن التعاقد متى انطبقت شروطها.',
			),
			array(
				'num'      => '08',
				'title_en' => 'PRODUCT AVAILABILITY',
				'body_en'  => 'All orders are subject to product availability.

In exceptional cases, a product may appear available on the website while it is actually out of stock due to inventory updates or simultaneous orders.

If a product is unavailable, we may offer the customer, depending on the circumstances:

- The option to choose an alternative product.
- Modification of the order.
- Waiting for the product to become available where appropriate.
- Cancellation of the relevant product or the entire order without charging the customer a cost arising from the unavailability.

No product will be replaced with another product without the customer\'s approval.',
				'title_ar' => 'توافر المنتجات',
				'body_ar'  => 'جميع الطلبات تخضع لتوافر المنتجات في المخزون.

قد يحدث في حالات استثنائية أن يظهر منتج متاحًا على الموقع بينما يكون قد نفد فعليًا بسبب تحديث المخزون أو تزامن عدة طلبات.

إذا لم يكن المنتج متاحًا، فسنتواصل مع العميل ونتيح له بحسب الحالة:

● اختيار منتج بديل.
● تعديل الطلب.
● انتظار توفر المنتج إذا كان ذلك مناسبًا.
● إلغاء المنتج أو الطلب دون تحميل العميل تكلفة بسببه.

ولا يتم استبدال منتج بمنتج آخر دون موافقة العميل.',
			),
			array(
				'num'      => '09',
				'title_en' => 'ACCURACY OF ORDER INFORMATION',
				'body_en'  => 'The customer is responsible for providing accurate and sufficient information to process the order, particularly:

- Name.
- Telephone number.
- Delivery address.
- Area and governorate.
- Product details.
- Quantity.
- Prescription power or size where selected by the customer.

If incorrect information provided by the customer prevents delivery or order fulfilment, the customer may be responsible for actual costs resulting from an additional delivery attempt, to the extent permitted by law and after being informed of such costs.',
				'title_ar' => 'صحة بيانات الطلب',
				'body_ar'  => 'يتحمل العميل مسؤولية تقديم بيانات صحيحة وكافية لتنفيذ الطلب، وبصفة خاصة:

● الاسم.
● رقم الهاتف.
● عنوان التوصيل.
● المنطقة والمحافظة.
● تفاصيل المنتج.
● الكمية.
● الدرجة أو المقاس إذا اختارهما العميل.

إذا تسبب خطأ في البيانات التي قدمها العميل في تعذر التوصيل أو تنفيذ الطلب، فقد يتحمل العميل التكاليف الفعلية المترتبة على إعادة محاولة التوصيل، بالقدر الذي يسمح به القانون وبعد إبلاغه بها.',
			),
			array(
				'num'      => '10',
				'title_en' => 'PAYMENT',
				'body_en'  => 'Payments are made using the methods available to the customer when placing the order.

Currently available payment methods include:

- Cash on Delivery.
- InstaPay in accordance with the agreed payment method.
- Electronic wallet payments in accordance with the agreed method.

lilylenses.com does not currently require customers to enter credit or debit card details to complete an order.

We will never ask you for:

- Your card PIN.
- Your bank account password.
- An OTP verification code.
- Login credentials for your banking or wallet application.

Do not share any such information with anyone claiming to represent Lily.

If you receive a suspicious request for confidential financial information in Lily\'s name, please contact us immediately at 01060760098.',
				'title_ar' => 'الدفع',
				'body_ar'  => 'تتم المدفوعات وفق الوسائل التي تظهر للعميل وقت تنفيذ الطلب.

وتشمل وسائل الدفع المتاحة حاليًا:

● الدفع النقدي عند الاستلام.
● الدفع عبر إنستا باي عند الاستلام أو وفق طريقة الدفع المتفق عليها.
● الدفع باستخدام محفظة إلكترونية وفق الطريقة المتفق عليها.

لا يطلب موقع lilylenses.com حاليًا إدخال بيانات بطاقة ائتمان أو خصم مباشر لإتمام الطلب.

كما أننا لا نطلب منك:

● الرقم السري للبطاقة.
● كلمة مرور الحساب البنكي.
● رمز التحقق OTP.
● بيانات تسجيل الدخول إلى تطبيق البنك أو المحفظة.

لا تشارك أيًا من هذه البيانات مع أي شخص يدّعي تمثيل «ليلي».

وإذا تلقيت طلبًا مشبوهًا للحصول على بيانات مالية سرية باسم «ليلي»، يرجى التواصل معنا فورًا على 01060760098.',
			),
			array(
				'num'      => '11',
				'title_en' => 'DELIVERY',
				'body_en'  => 'Orders are delivered in accordance with the Shipping and Delivery Policy published on the website, which forms an integral part of these Terms & Conditions.

Delivery charges are communicated to the customer according to the delivery area before the order is confirmed.

Published or estimated delivery times may be affected by circumstances beyond reasonable control, including weather conditions, road conditions or public events. We will make reasonable efforts to inform the customer of any significant delay when we become aware of it.

The customer\'s legal rights in the event of delayed delivery remain fully preserved.',
				'title_ar' => 'التوصيل',
				'body_ar'  => 'يتم توصيل الطلبات وفق سياسة الشحن والتوصيل المنشورة على الموقع، والتي تعد جزءًا مكملًا لهذه الشروط.

يتم توضيح مصروفات التوصيل للعميل وفق المنطقة قبل تأكيد الطلب.

ومواعيد التوصيل المعلنة أو المقدرة قد تتأثر بظروف خارجة عن السيطرة المعقولة، مثل الظروف الجوية أو الطرق أو الأحداث العامة، على أن نبذل جهدًا معقولًا لإبلاغ العميل بأي تأخير جوهري متى علمنا به.

وتظل حقوق العميل القانونية في حالة التأخر في التسليم محفوظة بالكامل.',
			),
			array(
				'num'      => '12',
				'title_en' => 'CANCELLING AN ORDER BEFORE DELIVERY',
				'body_en'  => 'Customers may request cancellation of an order by contacting Customer Service.

If the order has not yet been dispatched for delivery, it will be cancelled without charging the customer delivery costs that have not been incurred.

If the order has already been dispatched, the applicable rules regarding delivery costs and withdrawal from the contract shall apply in accordance with the law and the Shipping, Exchange and Return Policy published on the website.

Nothing in this section affects any mandatory right granted to the customer under Egyptian Consumer Protection Law.',
				'title_ar' => 'إلغاء الطلب قبل التوصيل',
				'body_ar'  => 'يمكن للعميل طلب إلغاء الطلب بالتواصل مع خدمة العملاء.

إذا لم يكن الطلب قد خرج للتوصيل، يتم إلغاؤه دون تحميل العميل مصروفات توصيل لم يتم تكبدها.

أما إذا كان الطلب قد خرج بالفعل للتوصيل، فتطبق القواعد الخاصة بمصاريف الشحن والعدول عن التعاقد وفقًا للقانون وسياسة الشحن والاستبدال والاسترجاع المعلنة على الموقع.

ولا يؤثر هذا البند على أي حق إلزامي يمنحه قانون حماية المستهلك للعميل.',
			),
			array(
				'num'      => '13',
				'title_en' => 'CIRCUMSTANCES IN WHICH LILY MAY CANCEL AN ORDER',
				'body_en'  => 'We may refuse or cancel an order before it is completed for legitimate reasons, including:

- The product is out of stock.
- We are unable to verify the order information.
- The delivery address is incorrect or insufficient.
- We are unable to contact the customer after making reasonable attempts to confirm the order.
- There is an obvious material error in the product information or price before order confirmation.
- There is a reasonable suspicion of fraudulent use of the website or ordering process.
- Repeated non-serious orders or repeated refusal to accept delivery in a manner that clearly constitutes abuse of the service.

We will attempt to inform the customer of the reason for cancellation whenever reasonably possible.

If Lily has received any payment relating to an order cancelled by us, any amount due shall be refunded in accordance with applicable law and the available payment method.',
				'title_ar' => 'الحالات التي يجوز لـ«ليلي» فيها إلغاء الطلب',
				'body_ar'  => 'يجوز لنا عدم قبول طلب أو إلغاؤه قبل إتمام تنفيذه لأسباب مشروعة، ومنها:

● نفاد المنتج من المخزون.
● عدم القدرة على التحقق من بيانات الطلب.
● عدم صحة أو عدم كفاية عنوان التوصيل.
● تعذر التواصل مع العميل بعد محاولات معقولة لتأكيد الطلب.
● وجود خطأ مادي واضح في بيانات المنتج أو السعر قبل تأكيد الطلب.
● الاشتباه المعقول في استخدام الموقع أو الطلب بصورة احتيالية.
● تكرار طلبات غير جادة أو رفض الاستلام بصورة متكررة وبشكل يسبب إساءة واضحة للخدمة.

وسنحاول إبلاغ العميل بسبب الإلغاء متى كان ذلك ممكنًا.

وإذا كانت «ليلي» قد تسلمت أي مبلغ يتعلق بطلب تم إلغاؤه من جانبها، فيتم رد المبلغ المستحق وفقًا للقانون وطريقة الدفع المتاحة.',
			),
			array(
				'num'      => '14',
				'title_en' => 'RIGHT OF WITHDRAWAL, EXCHANGE AND RETURN',
				'body_en'  => 'Exchange and return transactions are subject to Egyptian Consumer Protection Law and the detailed policy published on the website.

In general, consumers are granted legal rights relating to the exchange or return of goods within the periods established by law, subject to exceptions related to the nature, condition and packaging of certain products and whether they have been manufactured according to special specifications.

Consumers may also have additional rights to withdraw from distance contracts in the circumstances and within the periods specified by law.

Due to the health and personal nature of certain contact lens products, it may not be possible to accept the return of some products after their packaging has been opened or the products have been used where this makes it impossible to return the product to its original condition or safely resell it, but only to the extent permitted by law.',
				'title_ar' => 'حق العدول والاستبدال والاسترجاع',
				'body_ar'  => 'تخضع عمليات الاستبدال والاسترجاع لقانون حماية المستهلك المصري والسياسة التفصيلية المنشورة على الموقع.

وبوجه عام، يمنح القانون للمستهلك حقوقًا تتعلق باستبدال أو إعادة السلع خلال المدد المقررة قانونًا، مع وجود استثناءات ترتبط بطبيعة بعض المنتجات وحالتها وتغليفها وما إذا كانت مصنوعة وفق مواصفات خاصة.

كما يتمتع المستهلك في التعاقد عن بُعد بحقوق إضافية للعدول عن التعاقد في الحالات والمدد التي يحددها القانون.

وبالنظر إلى الطبيعة الصحية والشخصية لبعض منتجات العدسات اللاصقة، فقد يتعذر قبول إعادة بعض المنتجات بعد فتح عبوتها أو استخدامها عندما يجعل ذلك المنتج غير قابل لإعادته إلى حالته الأصلية أو إعادة بيعه بصورة آمنة، وذلك فقط في الحدود التي يسمح بها القانون.',
			),
			array(
				'num'      => '15',
				'title_en' => 'DEFECTIVE OR NON-CONFORMING PRODUCTS',
				'body_en'  => 'If a product is found to be defective or does not conform to the agreed or advertised specifications or description, the matter shall be handled in accordance with the rights and guarantees established by law.

The customer shall not bear additional costs resulting from addressing a confirmed product defect where legal responsibility rests with the supplier.

We may request photographs or information relating to the product, packaging, batch number or other information necessary to investigate the complaint, provided that this does not reduce the customer\'s legal rights.',
				'title_ar' => 'المنتجات المعيبة أو غير المطابقة',
				'body_ar'  => 'إذا ثبت وجود عيب في المنتج أو عدم مطابقته للمواصفات أو الوصف المتفق عليه أو المعلن عنه، يتم التعامل مع الحالة وفقًا للحقوق والضمانات التي يقررها القانون.

ولا يتحمل العميل تكلفة إضافية ناتجة عن معالجة عيب ثابت في المنتج متى كانت المسؤولية تقع قانونًا على المورد.

وقد نطلب من العميل صورًا أو بيانات عن المنتج والعبوة ورقم التشغيلة أو غير ذلك من المعلومات اللازمة لفحص الشكوى، على ألا يؤدي ذلك إلى الانتقاص من حقوقه القانونية.',
			),
			array(
				'num'      => '16',
				'title_en' => 'DEFECTS AFFECTING HEALTH OR SAFETY',
				'body_en'  => 'Customer safety is of particular importance to us.

If we become aware of a defect in a product that may cause harm to the user\'s health or safety, we will take the actions required by law, which may include:

- Suspending sales or distribution of the product.
- Contacting customers regarding the product where necessary.
- Warning customers not to use the product.
- Notifying the relevant authorities.
- Providing exchange, return or refund remedies depending on the circumstances.',
				'title_ar' => 'عيوب تمس الصحة أو السلامة',
				'body_ar'  => 'سلامة العملاء لها أولوية خاصة لدينا.

إذا علمنا بوجود عيب في منتج قد يسبب ضررًا لصحة أو سلامة المستخدم، نتخذ الإجراءات التي يفرضها القانون، والتي قد تشمل:

● وقف التعامل على المنتج.
● التواصل بشأن المنتج عند الحاجة.
● تحذير العملاء من استخدامه.
● إخطار الجهات المختصة.
● الاستبدال أو الاسترجاع أو رد القيمة بحسب الحالة.',
			),
			array(
				'num'      => '17',
				'title_en' => 'COMPLAINTS AND AFTER-SALES SERVICE',
				'body_en'  => 'Complaints may be submitted through:

Customer Service & WhatsApp: 01060760098

We aim to review and respond to complaints as quickly as reasonably possible depending on the nature of each case.

We do not guarantee a response within a specific number of hours in every case, as some complaints may require reviewing the product, batch number or communicating with another party.

All complaints are handled within the timeframes and rights established by law.',
				'title_ar' => 'الشكاوى وخدمة ما بعد البيع',
				'body_ar'  => 'يمكن تقديم الشكاوى من خلال:

خدمة العملاء وواتساب: 01060760098

نسعى إلى مراجعة الشكاوى والرد عليها في أسرع وقت ممكن بحسب طبيعة الحالة.

ولا نضمن عبارة «الرد خلال ساعات» في كل حالة، لأن بعض الشكاوى قد تحتاج إلى مراجعة المنتج أو رقم التشغيلة أو التواصل مع جهة أخرى.

ويتم التعامل مع جميع الشكاوى في إطار المدد والحقوق التي يحددها القانون.',
			),
			array(
				'num'      => '18',
				'title_en' => 'CUSTOMER ACCOUNT',
				'body_en'  => 'The website may allow customers to create an optional account in order to:

- Track orders.
- Save certain delivery information.
- Make future orders easier to process.

Customers are responsible for maintaining the confidentiality of their account login information and must not share it with others.

You must inform us immediately if you suspect that your account has been used without authorization.

We may suspend or disable an account where it is proven to have been used for fraudulent or unlawful activity or in a manner that harms the website or the rights of others, while respecting the customer\'s legal rights regarding any valid outstanding orders.',
				'title_ar' => 'حساب العميل',
				'body_ar'  => 'قد يتيح الموقع إنشاء حساب اختياري للعميل من أجل:

● متابعة الطلبات.
● حفظ بعض بيانات التوصيل.
● تسهيل تنفيذ طلبات لاحقة.

يتحمل العميل مسؤولية المحافظة على سرية بيانات الدخول الخاصة بحسابه وعدم مشاركتها مع الغير.

ويجب إبلاغنا فورًا عند الاشتباه في استخدام الحساب دون إذن.

يجوز لنا تعليق أو إيقاف حساب إذا ثبت استخدامه في نشاط احتيالي أو مخالف للقانون أو بصورة تضر بالموقع أو بحقوق الآخرين، مع مراعاة الحقوق القانونية للعميل فيما يتعلق بأي طلبات صحيحة قائمة.',
			),
			array(
				'num'      => '19',
				'title_en' => 'USE OF THE WEBSITE',
				'body_en'  => 'Users must not use the website for any unlawful activity or for the purpose of:

- Fraud.
- Impersonating another person.
- Hacking or disrupting the website.
- Introducing malicious software.
- Accessing other users\' information without authorization.
- Misusing offers or ordering systems.
- Violating intellectual property rights.

We may take appropriate technical or legal measures to protect the website and its users in the event of misuse.',
				'title_ar' => 'استخدام الموقع',
				'body_ar'  => 'يلتزم المستخدم بعدم استخدام الموقع في أي نشاط غير مشروع أو يهدف إلى:

● الاحتيال.
● انتحال شخصية الغير.
● اختراق الموقع أو تعطيله.
● إدخال برمجيات ضارة.
● الحصول دون تصريح على بيانات مستخدمين آخرين.
● إساءة استخدام العروض أو أنظمة الطلب.
● انتهاك حقوق الملكية الفكرية.

ويجوز لنا اتخاذ الإجراءات التقنية أو القانونية المناسبة لحماية الموقع والمستخدمين عند وقوع إساءة استخدام.',
			),
			array(
				'num'      => '20',
				'title_en' => 'INTELLECTUAL PROPERTY',
				'body_en'  => 'Intellectual property rights relating to content owned by Lily, including original text, designs, images for which we hold the relevant rights, logos and trademarks, belong to their respective rights holders.

Such content may not be copied, republished or used commercially without prior authorization except to the extent permitted by law.

The appearance of a trademark or content belonging to another party on the website does not transfer ownership of that trademark or content to Lily.',
				'title_ar' => 'الملكية الفكرية',
				'body_ar'  => 'تعود حقوق الملكية الفكرية المتعلقة بالمحتوى الذي تملكه «ليلي»، بما في ذلك النصوص الأصلية والتصميمات والصور التي نملك حقوقها والشعارات والعلامات التجارية، إلى أصحاب الحقوق فيها.

ولا يجوز نسخ هذا المحتوى أو إعادة نشره أو استخدامه تجاريًا دون تصريح مسبق، إلا في الحدود التي يسمح بها القانون.

ولا يعني وجود أي علامة تجارية أو محتوى تابع لجهة أخرى على الموقع انتقال ملكيته إلى «ليلي».',
			),
			array(
				'num'      => '21',
				'title_en' => 'LIMITATION OF LIABILITY',
				'body_en'  => 'Lily shall not be responsible for damage proven to have resulted exclusively from the customer\'s use of a product in clear violation of usage instructions, use of a product after its expiry date, sharing contact lenses with another person, or providing incorrect information regarding prescription power or size, to the extent permitted by law.

However, these Terms & Conditions do not exclude any liability imposed by law or reduce any mandatory consumer rights or guarantees.

In particular, these Terms & Conditions shall not be used to exclude legal liability where damage results from:

- A product defect.
- An error in preparing, storing, packaging or handling the product.
- Required information or warnings not being provided.
- Any breach giving rise to legal liability.

Accordingly, liability is not automatically limited in all circumstances to the value of the product and shall instead be determined according to the nature of the incident and the applicable legal rules.',
				'title_ar' => 'حدود المسؤولية',
				'body_ar'  => 'لا تتحمل «ليلي» مسؤولية الضرر الذي يثبت أنه نتج حصرًا عن استخدام العميل للمنتج بصورة مخالفة بوضوح لتعليمات الاستخدام أو عن استخدام منتج بعد انتهاء صلاحيته أو عن مشاركة العدسات مع شخص آخر أو عن إدخال العميل بيانات غير صحيحة بشأن الدرجة أو المقاس، وذلك بالقدر الذي يسمح به القانون.

ومع ذلك، لا تتضمن هذه الشروط أي إعفاء لـ«ليلي» من مسؤولية يفرضها القانون، ولا أي تخفيض للحقوق أو الضمانات الإلزامية المقررة للمستهلك.

وعلى وجه الخصوص، لا تستخدم هذه الشروط للإعفاء من المسؤولية القانونية إذا كان الضرر ناتجًا عن:

● عيب في المنتج.
● خطأ في إعداد المنتج أو حفظه أو تعبئته أو تداوله.
● معلومات أو تحذيرات واجبة لم يتم تقديمها.
● إخلال تترتب عليه مسؤولية بموجب القانون.

وبالتالي لا تقتصر المسؤولية تلقائيًا في جميع الحالات على قيمة المنتج، وإنما تحدد بحسب طبيعة الواقعة والقواعد القانونية الواجبة التطبيق.',
			),
			array(
				'num'      => '22',
				'title_en' => 'PRIVACY POLICY',
				'body_en'  => 'The collection and use of customer personal data are governed by the Privacy Policy published on lilylenses.com, which explains:

- The data we collect.
- The purposes for which it is used.
- How it is protected.
- How long it is retained.
- Rights relating to that data.
- How prescription information is handled.

The Privacy Policy is separate from these Terms & Conditions where the law requires separate consent from the customer.',
				'title_ar' => 'سياسة الخصوصية',
				'body_ar'  => 'يخضع جمع واستخدام البيانات الشخصية للعميل إلى سياسة الخصوصية المنشورة على lilylenses.com، والتي توضح:

● البيانات التي نجمعها.
● أغراض استخدامها.
● كيفية حمايتها.
● مدة الاحتفاظ بها.
● الحقوق المتعلقة بها.
● التعامل مع بيانات درجة النظر.

وتعد سياسة الخصوصية مستقلة عن هذه الشروط فيما يتعلق بالحالات التي يستلزم فيها القانون الحصول على موافقة منفصلة من العميل.',
			),
			array(
				'num'      => '23',
				'title_en' => 'FORCE MAJEURE AND CIRCUMSTANCES BEYOND REASONABLE CONTROL',
				'body_en'  => 'We shall not be responsible for delay or failure to perform where it results directly from circumstances beyond reasonable control, including natural disasters, emergency government decisions, widespread disruption of communications or transportation networks, or similar events that could not reasonably have been prevented.

In such circumstances, we will make reasonable efforts to reduce the impact of the delay and inform the customer where necessary.

This does not affect any mandatory rights granted to consumers by law.',
				'title_ar' => 'القوة القاهرة والظروف الخارجة عن السيطرة',
				'body_ar'  => 'لا نكون مسؤولين عن التأخير أو عدم التنفيذ الذي يحدث بصورة مباشرة نتيجة ظرف خارج عن السيطرة المعقولة، مثل الكوارث الطبيعية أو القرارات الحكومية الطارئة أو تعطل واسع في شبكات الاتصالات أو النقل أو أحداث مماثلة لا يمكن منعها بصورة معقولة.

وفي هذه الحالات، نبذل جهدًا معقولًا لتقليل أثر التأخير وإبلاغ العميل عندما يكون ذلك ضروريًا.

ولا يؤثر ذلك على الحقوق الإلزامية التي يمنحها القانون للمستهلك.',
			),
			array(
				'num'      => '24',
				'title_en' => 'APPLICABLE LAW',
				'body_en'  => 'These Terms & Conditions and any transaction conducted through the website are governed by the laws of the Arab Republic of Egypt, particularly mandatory rules relating to consumer protection, personal data protection and other relevant legislation.

In the event of a dispute, the competent Egyptian courts shall have jurisdiction in accordance with the applicable legal rules governing jurisdiction.

This does not prevent consumers from contacting the Egyptian Consumer Protection Agency or any other competent authority where they have the right to do so.',
				'title_ar' => 'القانون الواجب التطبيق',
				'body_ar'  => 'تخضع هذه الشروط وأي تعامل يتم من خلال الموقع إلى قوانين جمهورية مصر العربية، وعلى الأخص القواعد الإلزامية المتعلقة بحماية المستهلك وحماية البيانات الشخصية وغيرها من التشريعات ذات الصلة.

وفي حالة حدوث نزاع، تكون المحاكم المصرية المختصة هي صاحبة الاختصاص وفقًا لقواعد الاختصاص المقررة قانونًا.

ولا يمنع ذلك المستهلك من اللجوء إلى جهاز حماية المستهلك أو أي جهة مختصة أخرى عندما يكون له الحق في ذلك.',
			),
			array(
				'num'      => '25',
				'title_en' => 'CONSUMER PROTECTION AGENCY',
				'body_en'  => 'If a complaint relating to a purchase cannot be resolved directly with us, the customer retains the right to contact the Egyptian Consumer Protection Agency in accordance with the methods and procedures announced by the Agency.

Nothing in these Terms & Conditions restricts the consumer\'s right to submit a complaint to the Agency or exercise any right guaranteed by law.',
				'title_ar' => 'جهاز حماية المستهلك',
				'body_ar'  => 'إذا تعذر حل شكوى متعلقة بالشراء بصورة مباشرة معنا، يظل من حق العميل اللجوء إلى جهاز حماية المستهلك المصري وفقًا للوسائل والإجراءات التي يعلنها الجهاز.

ولا تتضمن هذه الشروط أي قيد على حق المستهلك في تقديم شكوى إلى الجهاز أو ممارسة أي حق يكفله له القانون.',
			),
			array(
				'num'      => '26',
				'title_en' => 'AMENDMENTS TO THE TERMS & CONDITIONS',
				'body_en'  => 'We may update these Terms & Conditions from time to time due to changes in our services, products, procedures or legal requirements.

The updated version will be published on this page together with an updated "Last Updated" date.

Amendments apply to new orders from their effective date.

An order that has already been confirmed shall remain subject to the Terms & Conditions in force at the time of confirmation unless the amendment is required by law or is more beneficial to the customer.',
				'title_ar' => 'تعديل الشروط والأحكام',
				'body_ar'  => 'قد نقوم بتحديث هذه الشروط من وقت إلى آخر بسبب تغيير الخدمات أو المنتجات أو الإجراءات أو المتطلبات القانونية.

يتم نشر النسخة المحدثة على هذه الصفحة مع تعديل تاريخ «آخر تحديث».

تسري التعديلات على الطلبات الجديدة اعتبارًا من تاريخ سريانها.

أما الطلب الذي سبق تأكيده، فيظل خاضعًا للشروط التي كانت سارية وقت تأكيده، ما لم يكن التعديل واجب التطبيق بموجب القانون أو أكثر فائدة للعميل.',
			),
			array(
				'num'      => '27',
				'title_en' => 'SEVERABILITY',
				'body_en'  => 'If any provision of these Terms & Conditions is found to be invalid or unenforceable, this alone shall not invalidate the remaining provisions, which shall continue to apply to the extent permitted by law.',
				'title_ar' => 'قابلية فصل الأحكام',
				'body_ar'  => 'إذا ثبت أن أحد بنود هذه الشروط غير صحيح أو غير قابل للتنفيذ قانونًا، فلا يؤدي ذلك وحده إلى بطلان بقية البنود، وتظل الأحكام الأخرى سارية في الحدود التي يسمح بها القانون.',
			),
			array(
				'num'      => '28',
				'title_en' => 'PRIORITY OF LAW',
				'body_en'  => 'If any provision of these Terms & Conditions conflicts with a mandatory consumer right established under Egyptian law, the legal provision shall prevail and the relevant contractual provision shall apply only to the extent that it does not conflict with that law.',
				'title_ar' => 'أولوية القانون',
				'body_ar'  => 'إذا تعارض أي بند في هذه الشروط مع حق إلزامي مقرر للمستهلك بموجب القانون المصري، تكون الأولوية للنص القانوني، ويطبق البند فقط في الحدود التي لا تتعارض معه.',
			),
			array(
				'num'      => '29',
				'title_en' => 'CONTACT US',
				'body_en'  => 'For any questions regarding orders or these Terms & Conditions, you may contact us through:

Lily – Lily Original Lenses

Customer Service & WhatsApp: 01060760098

Website: lilylenses.com',
				'title_ar' => 'التواصل معنا',
				'body_ar'  => 'لأي استفسار عن الطلبات أو هذه الشروط والأحكام، يمكنك التواصل معنا من خلال:

ليلي – Lily Original Lenses

خدمة العملاء وواتساب: 01060760098

الموقع: lilylenses.com',
			),
		),
	);

	return $content;
}

/**
 * Default Terms settings (empty = use the approved content above).
 *
 * @return array
 */
function lily_terms_settings_defaults() {
	$defaults = array(
		'terms_hero_title'    => '',
		'terms_hero_title_ar' => '',
		'terms_hero_sub'      => '',
		'terms_hero_sub_ar'   => '',
	);

	for ( $i = 1; $i <= 29; $i++ ) {
		$defaults[ "terms_{$i}_title" ]    = '';
		$defaults[ "terms_{$i}_title_ar" ] = '';
		$defaults[ "terms_{$i}_body" ]     = '';
		$defaults[ "terms_{$i}_body_ar" ]  = '';
	}

	return $defaults;
}

/**
 * Sanitize Terms settings.
 *
 * @param array $raw Raw submitted values.
 * @return array
 */
function lily_sanitize_terms_settings( array $raw ) {
	$data = array();

	$data['terms_hero_title']    = isset( $raw['terms_hero_title'] ) ? sanitize_text_field( $raw['terms_hero_title'] ) : '';
	$data['terms_hero_title_ar'] = isset( $raw['terms_hero_title_ar'] ) ? sanitize_text_field( $raw['terms_hero_title_ar'] ) : '';
	$data['terms_hero_sub']      = isset( $raw['terms_hero_sub'] ) ? sanitize_textarea_field( $raw['terms_hero_sub'] ) : '';
	$data['terms_hero_sub_ar']   = isset( $raw['terms_hero_sub_ar'] ) ? sanitize_textarea_field( $raw['terms_hero_sub_ar'] ) : '';

	for ( $i = 1; $i <= 29; $i++ ) {
		$data[ "terms_{$i}_title" ]    = isset( $raw[ "terms_{$i}_title" ] ) ? sanitize_text_field( $raw[ "terms_{$i}_title" ] ) : '';
		$data[ "terms_{$i}_title_ar" ] = isset( $raw[ "terms_{$i}_title_ar" ] ) ? sanitize_text_field( $raw[ "terms_{$i}_title_ar" ] ) : '';
		$data[ "terms_{$i}_body" ]     = isset( $raw[ "terms_{$i}_body" ] ) ? sanitize_textarea_field( $raw[ "terms_{$i}_body" ] ) : '';
		$data[ "terms_{$i}_body_ar" ]  = isset( $raw[ "terms_{$i}_body_ar" ] ) ? sanitize_textarea_field( $raw[ "terms_{$i}_body_ar" ] ) : '';
	}

	return wp_parse_args( $data, lily_terms_settings_defaults() );
}

/**
 * Convert a section number to Eastern Arabic numerals on Arabic requests.
 *
 * Presentation of the same stored value (not separate content), so the
 * existing two-digit editorial design is preserved in both languages.
 *
 * @param string $num Two-digit number, e.g. "01".
 * @return string
 */
function lily_terms_num( $num ) {
	$num = (string) $num;

	if ( function_exists( 'lily_is_arabic_request' ) && lily_is_arabic_request() ) {
		// Multibyte-safe digit map (strtr is byte-wise and corrupts UTF-8).
		return str_replace(
			array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ),
			array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ),
			$num
		);
	}

	return $num;
}

/**
 * Render a Terms body inside the section body container.
 *
 * Stored plain text becomes structured markup: blank-line separated blocks
 * become paragraphs, and blocks whose lines start with a bullet marker
 * ("-", "●" and friends) become a real list. Everything is escaped; only
 * the list/paragraph elements below (styled by the scoped Terms CSS) are
 * added. Stored content itself is never altered.
 *
 * @param string $body Stored body text.
 */
function lily_terms_body_html( $body ) {
	$body   = str_replace( array( "\r\n", "\r" ), "\n", (string) $body );
	$blocks = preg_split( "/\n\s*\n/", trim( $body ) );
	$out    = '';

	if ( ! is_array( $blocks ) ) {
		return;
	}

	foreach ( $blocks as $block ) {
		$lines = array_values(
			array_filter(
				array_map( 'trim', explode( "\n", $block ) ),
				static function ( $line ) {
					return '' !== $line;
				}
			)
		);

		if ( empty( $lines ) ) {
			continue;
		}

		$is_list = true;

		foreach ( $lines as $line ) {
			if ( ! preg_match( '/^([-–—•●▪*])\s+/u', $line ) ) {
				$is_list = false;
				break;
			}
		}

		if ( $is_list ) {
			$out .= '<ul class="lily-terms-row__list">';

			foreach ( $lines as $line ) {
				$item = preg_replace( '/^([-–—•●▪*])\s+/u', '', $line );
				$out .= '<li>' . esc_html( $item ) . '</li>';
			}

			$out .= '</ul>';
		} else {
			$out .= '<p class="lily-terms-row__text">' . esc_html( implode( ' ', $lines ) ) . '</p>';
		}
	}

	echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- every string escaped above.
}

/**
 * Render the Terms Page tab panels on the existing Lily admin page.
 *
 * @param array $settings Current settings.
 */
function lily_render_terms_fields( $settings ) {
	$approved = lily_terms_approved_content();

	echo '<section id="lily-tab-terms" class="lily-admin-panel"><h2>' . esc_html__( 'Terms Page', 'lily' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Every field below is optional. Leave anything blank to keep the approved Terms content.', 'lily' ) . '</p>';

	echo '<h3>' . esc_html__( 'Terms Page — English', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown to English visitors.', 'lily' ) . '</p>';
	lily_admin_text_field( $settings, 'terms_hero_title', esc_html__( 'Hero Title', 'lily' ) );
	lily_admin_textarea_field( $settings, 'terms_hero_sub', esc_html__( 'Hero Intro', 'lily' ) );

	echo '<h3>' . esc_html__( 'Terms Sections — English', 'lily' ) . '</h3>';
	for ( $i = 1; $i <= 29; $i++ ) {
		$fallback = isset( $approved['sections'][ $i - 1 ] ) ? $approved['sections'][ $i - 1 ] : array();
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Section %d', 'lily' ), $i ) ) . ' — ' . esc_html( $fallback['title_en'] ?? '' ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[terms_%1$d_title]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "terms_{$i}_title" ] ?? '' ), esc_attr__( 'Section heading', 'lily' ) );
		printf( '<textarea name="lily_homepage[terms_%1$d_body]" rows="6" placeholder="%2$s">%3$s</textarea>', absint( $i ), esc_attr__( 'Section content (blank line = new paragraph, "- " = bullet)', 'lily' ), esc_textarea( $settings[ "terms_{$i}_body" ] ?? '' ) );
		echo '</fieldset>';
	}

	echo '<h3>' . esc_html__( 'Terms Page', 'lily' ) . ' — ' . esc_html__( 'العربية', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown on Arabic pages only. Leave any field blank to reuse its English text.', 'lily' ) . '</p>';
	lily_admin_text_field( $settings, 'terms_hero_title_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Hero Title', 'lily' ) ) );
	lily_admin_textarea_field( $settings, 'terms_hero_sub_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Hero Intro', 'lily' ) ) );

	echo '<h3>' . esc_html__( 'Terms Sections', 'lily' ) . ' — ' . esc_html__( 'العربية', 'lily' ) . '</h3>';
	for ( $i = 1; $i <= 29; $i++ ) {
		$fallback = isset( $approved['sections'][ $i - 1 ] ) ? $approved['sections'][ $i - 1 ] : array();
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Section %d (Arabic)', 'lily' ), $i ) ) . ' — ' . esc_html( $fallback['title_ar'] ?? '' ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[terms_%1$d_title_ar]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "terms_{$i}_title_ar" ] ?? '' ), esc_attr__( 'Section heading (Arabic)', 'lily' ) );
		printf( '<textarea name="lily_homepage[terms_%1$d_body_ar]" rows="6" placeholder="%2$s">%3$s</textarea>', absint( $i ), esc_attr__( 'Section content (Arabic)', 'lily' ), esc_textarea( $settings[ "terms_{$i}_body_ar" ] ?? '' ) );
		echo '</fieldset>';
	}

	echo '</section>';
}
