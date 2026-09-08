<?php
/**
 * Lily Returns & Exchange Policy dashboard fields (slug: returns-exchange).
 *
 * Reuses the existing Lily settings system (same option, same save flow,
 * same admin helpers as terms/shipping/faq/contact): the returns_* keys
 * live inside `lily_homepage_settings` and render as a "Returns Page" tab
 * on the existing Lily admin page. The page template reuses the Terms &
 * Conditions markup and CSS classes, so no new design system is created.
 *
 * Single source of truth: lily_returns_approved_content() holds the
 * complete approved English + Arabic policy verbatim. The template uses it
 * as fallback and the initial DB population copies it into the dashboard
 * fields, so the brand owner can edit both languages without code.
 *
 * Note: this page has no eyebrow element by design (title appears once).
 *
 * @package Lily
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Complete approved Returns & Exchange Policy content (verbatim source).
 *
 * @return array
 */
function lily_returns_approved_content() {
	static $content = null;

	if ( null !== $content ) {
		return $content;
	}

	$content = array(
		'hero_title_en' => 'Returns & Exchange Policy',
		'hero_title_ar' => 'سياسة الاستبدال والاسترجاع',
		'hero_intro_en' => 'Everything you need to know about exchanging or returning your order, including unopened products, manufacturing defects, incorrect products, refunds and your legal rights.

At Lily – Original Lily Lenses, we aim to ensure that the products you receive are safe, original and consistent with the specifications confirmed in your order.

Because contact lenses are products that are used directly on the eye, the condition of the original packaging and its safety seal is especially important. For this reason, the possibility of exchanging or returning a product may differ depending on whether the product remains unopened in its original condition or has been opened or used.

This policy is not intended to limit any right granted to the customer under Egyptian Consumer Protection Law. If any provision of this policy conflicts with a mandatory legal right, the law takes priority.',
		'hero_intro_ar' => 'كل ما تحتاجين إلى معرفته عن استبدال أو استرجاع طلبك، بما في ذلك المنتجات غير المفتوحة، وعيوب الصناعة، والمنتجات غير المطابقة، واسترداد المبالغ، وحقوقك القانونية.

في ليلي – Original Lily Lenses نحرص على أن تصلك المنتجات سليمة وأصلية ومطابقة للمواصفات التي تم تأكيدها في طلبك.

ونظرًا إلى أن العدسات اللاصقة من المنتجات التي تُستخدم بصورة مباشرة على العين، فإن سلامة العبوة وختمها الأصلي لها أهمية خاصة. لذلك تختلف إمكانية الاستبدال أو الاسترجاع بحسب ما إذا كان المنتج ما زال مغلقًا بحالته الأصلية أو تم فتحه أو استخدامه.

لا تهدف هذه السياسة إلى الانتقاص من أي حق يمنحه قانون حماية المستهلك المصري للعميل، وفي حالة تعارض أي حكم فيها مع حق إلزامي مقرر قانونًا، تكون الأولوية للقانون.',
		'sections'      => array(
			array(
				'num'      => '01',
				'title_en' => 'EXCHANGE OR RETURN WITHOUT A DEFECT',
				'body_en'  => 'Subject to the consumer rights and legal exceptions provided by law, a customer may request an exchange or return within the legally prescribed period if the product remains in its original condition and is eligible for return.

For contact lenses, a return without the presence of a defect requires that the original product packaging remain completely sealed and that the safety seal has not been removed, the packaging has not been opened and the lenses have not been used in any way.

If the original packaging has been opened, its seal has been removed or the lenses have been tried, a return cannot be accepted simply because of:

- Disliking the colour after trying it.
- Wanting to choose a different colour.
- Changing one\'s mind.
- Discovering that the prescription or size selected by the customer is not suitable.

This applies where the product delivered matches the specifications requested by the customer and contains no defect.

This is due to the hygienic and personal nature of the product and the fact that opened contact lenses cannot be restored to the same hygienic condition they were in before opening.

This restriction does not apply where the product is defective or does not conform to the order.',
				'title_ar' => 'الاستبدال أو الاسترجاع دون وجود عيب',
				'body_ar'  => 'وفقًا للحقوق المقررة للمستهلك والاستثناءات التي يسمح بها القانون، يمكن طلب استبدال أو إعادة المنتج خلال المدة القانونية المقررة إذا ظل المنتج في حالته الأصلية وكان صالحًا للإعادة.

وبالنسبة للعدسات اللاصقة، يشترط لقبول الإرجاع دون وجود عيب أن تكون العبوة الأصلية للمنتج مغلقة بالكامل ولم يتم إزالة الختم أو فتح العبوة أو استخدام العدسات بأي صورة.

إذا تم فتح العبوة الأصلية أو إزالة ختمها أو تجربة العدسات، فلا يمكن قبول الإرجاع لمجرد:

- عدم الإعجاب باللون بعد التجربة.
- الرغبة في اختيار لون مختلف.
- تغيير الرأي.
- اكتشاف أن الدرجة أو المقاس الذي اختاره العميل لا يناسبه.

وذلك متى كان المنتج الذي تم تسليمه مطابقًا للمواصفات التي طلبها العميل ولم يوجد به عيب.

ويرجع ذلك إلى الطبيعة الصحية والشخصية للمنتج وعدم إمكانية إعادة العدسات المفتوحة إلى نفس الحالة الصحية التي كانت عليها قبل فتحها.

ولا يسري هذا القيد على حالات وجود عيب في المنتج أو عدم مطابقته للطلب.',
			),
			array(
				'num'      => '02',
				'title_en' => 'UNOPENED PRODUCTS',
				'body_en'  => 'If the contact lens packaging remains sealed with its original safety seal and has not been used, opened or damaged, the customer may contact us to request an exchange or return within the legally prescribed period.

The product must remain in the same condition in which it was received and must be suitable for safe resale.

Return shipping costs, where applicable, are governed by Egyptian Consumer Protection Law, the rules relating to distance contracts and the Shipping & Delivery Policy published on the website.',
				'title_ar' => 'المنتجات التي لم يتم فتحها',
				'body_ar'  => 'إذا كانت عبوة العدسات ما زالت مغلقة بختمها الأصلي ولم يتم استخدامها أو فتحها أو إتلاف تغليفها، فيمكن للعميل التواصل معنا لطلب الاستبدال أو الاسترجاع خلال المدة المقررة قانونًا.

ويجب أن يكون المنتج في الحالة نفسها التي تم استلامه بها وقابلًا لإعادة بيعه بصورة آمنة.

وتطبق مصروفات إعادة الشحن، إن وجدت، وفقًا لقانون حماية المستهلك والأحكام الخاصة بالتعاقد عن بُعد وسياسة الشحن والتوصيل المعلنة على الموقع.',
			),
			array(
				'num'      => '03',
				'title_en' => 'DIFFERENCE IN COLOUR AFTER TRYING THE LENSES',
				'body_en'  => 'The final appearance of coloured contact lenses may vary from one person to another due to factors such as:

- Natural eye colour.
- Lighting.
- Skin tone.
- Photography.
- Mobile phone or computer screen settings.

For this reason, simply not liking the colour after opening and trying the product is not, by itself, considered a defect where the colour and model sent match the order.

However, if a colour different from the one confirmed in the order was sent, this is considered a non-conformity with the order and will be handled under the section relating to an incorrect or non-conforming product.',
				'title_ar' => 'اختلاف اللون بعد التجربة',
				'body_ar'  => 'قد يختلف المظهر النهائي للعدسات الملونة من شخص إلى آخر نتيجة عوامل مثل:

- لون العين الطبيعي.
- الإضاءة.
- لون البشرة.
- التصوير.
- إعدادات شاشة الهاتف أو الكمبيوتر.

ولذلك لا يعد عدم الإعجاب باللون بعد فتح المنتج وتجربته وحده عيبًا في المنتج، متى كان اللون والموديل الذي تم إرساله مطابقًا للطلب.

ومع ذلك، إذا تم إرسال لون مختلف عن اللون الذي تم تأكيده، فإن ذلك يعد عدم مطابقة للطلب ويتم التعامل معه وفقًا لبند المنتج الخطأ أو غير المطابق.',
			),
			array(
				'num'      => '04',
				'title_en' => 'SELECTING A PRESCRIPTION OR SIZE',
				'body_en'  => 'If the customer selects a particular prescription or size and the product is delivered with the same prescription or size confirmed in the order, the fact that the prescription or size does not suit the customer is not considered a defect in the product.

Lily does not perform eye examinations and does not determine the appropriate medical prescription for the customer. The customer is therefore responsible for confirming the prescription and size that are appropriate for them and is advised to consult an eye doctor or qualified specialist when necessary.

If a prescription or size different from the one confirmed in the order was sent, we will be responsible for correcting the error without charging the customer any additional cost.',
				'title_ar' => 'اختيار الدرجة أو المقاس',
				'body_ar'  => 'إذا قام العميل باختيار درجة نظر أو مقاس معين وتم إرسال المنتج بنفس الدرجة أو المقاس الذي تم تأكيده، فلا يعتبر عدم ملاءمة هذه الدرجة أو المقاس للعميل عيبًا في المنتج.

ليلي لا تقوم بفحص النظر ولا تحدد الدرجة الطبية المناسبة للعميل، ولذلك يجب على العميل التأكد من الدرجة والمقاس المناسبين له، ويفضل الرجوع إلى طبيب عيون أو مختص مؤهل عند الحاجة.

أما إذا تم إرسال درجة أو مقاس مختلف عما تم تأكيده في الطلب، فإننا نتحمل مسؤولية تصحيح الخطأ دون تحميل العميل تكلفة إضافية.',
			),
			array(
				'num'      => '05',
				'title_en' => 'DEFECTIVE OR NON-CONFORMING PRODUCTS',
				'body_en'  => 'If a product is defective or does not conform to the agreed specifications, the customer retains the legal rights provided to them.

Examples may include:

- A defect in the contact lens itself.
- A problem with the packaging or solution that is not caused by misuse by the customer.
- Sending a product, colour, prescription or size different from the one confirmed in the order.

In accordance with the law and depending on the circumstances, the customer may request that the product be exchanged or returned and that its value be refunded without any additional cost to the customer.

Egyptian Consumer Protection Law provides the consumer with rights relating to defective or non-conforming goods within 30 days from the date of receiving the product.',
				'title_ar' => 'المنتجات المعيبة أو غير المطابقة',
				'body_ar'  => 'إذا كان المنتج به عيب أو لم يكن مطابقًا للمواصفات المتفق عليها، يحتفظ العميل بحقوقه القانونية المقررة.

ويشمل ذلك، على سبيل المثال:

- وجود خلل في العدسة نفسها.
- مشكلة في العبوة أو المحلول لا ترجع إلى سوء استخدام العميل.
- إرسال منتج أو لون أو درجة أو مقاس مختلف عما تم تأكيده.

ويكون للعميل، وفقًا للقانون وبحسب الحالة، طلب استبدال المنتج أو إعادته واسترداد قيمته دون تكلفة إضافية عليه.

ويقرر قانون حماية المستهلك حق المستهلك في التعامل مع السلعة المعيبة أو غير المطابقة خلال 30 يومًا من تاريخ استلامها.',
			),
			array(
				'num'      => '06',
				'title_en' => 'A DEFECT DISCOVERED AFTER OPENING THE PRODUCT',
				'body_en'  => 'Opening the contact lens packaging does not prevent the customer from submitting a complaint regarding a manufacturing defect, as some defects may not be discoverable until the product has been opened.

If the customer notices a problem that may indicate a defect in the lens, they should stop using it immediately, especially if its use is accompanied by pain, redness, irritation or changes in vision.

Where possible, please keep:

- The lens.
- The original packaging.
- The outer carton.
- Batch information.
- Any parts related to the product.

These should be retained until the complaint has been reviewed.

Opening the product does not automatically mean that the complaint will be accepted or rejected. Each case will be assessed based on the nature of the problem, the available information and the customer\'s legal rights.',
				'title_ar' => 'المنتج المعيب بعد فتح العبوة',
				'body_ar'  => 'فتح عبوة العدسات لا يمنع العميل من تقديم شكوى بشأن عيب صناعة؛ إذ قد لا يمكن اكتشاف بعض العيوب إلا بعد فتح المنتج.

إذا لاحظ العميل مشكلة يشتبه في أنها عيب في العدسة، يجب التوقف عن استخدامها فورًا، خصوصًا إذا صاحب استخدامها ألم أو احمرار أو تهيج أو تغير في الرؤية.

ويرجى الاحتفاظ، قدر الإمكان، بـ:

- العدسة.
- العبوة الأصلية.
- الكرتونة.
- بيانات التشغيلة.
- أي أجزاء متعلقة بالمنتج.

وذلك إلى حين مراجعة الشكوى.

فتح المنتج لا يعني تلقائيًا قبول أو رفض الشكوى، ويتم تقييم الحالة بناءً على طبيعة المشكلة والبيانات المتاحة وحقوق العميل القانونية.',
			),
			array(
				'num'      => '07',
				'title_en' => 'REPORTING A PROBLEM',
				'body_en'  => 'We recommend contacting us as soon as possible after discovering a problem. For visible errors or damage, contacting us preferably within the first 24 hours can help us review the case more quickly.

However, the 24-hour period is not a deadline after which the customer\'s legal rights regarding a defective or non-conforming product are lost. The legal time periods provided by law remain applicable.',
				'title_ar' => 'الإبلاغ عن المشكلة',
				'body_ar'  => 'نوصي بالتواصل معنا في أسرع وقت ممكن عند اكتشاف أي مشكلة، ويفضل خلال أول 24 ساعة بالنسبة إلى الأخطاء أو التلف الظاهر؛ فهذا يساعدنا على مراجعة الحالة بسرعة.

ومع ذلك، فإن مهلة الـ24 ساعة ليست مهلة تسقط بعدها الحقوق القانونية للعميل بشأن المنتج المعيب أو غير المطابق، وتظل المدد القانونية المقررة سارية.',
			),
			array(
				'num'      => '08',
				'title_en' => 'HOW TO REQUEST AN EXCHANGE OR RETURN',
				'body_en'  => 'You can contact us through:

Customer Service, Complaints & WhatsApp:

01060760098

Please provide:

- Your order number or the phone number used for the purchase.
- A description of the issue.

Depending on the circumstances, we may request:

- A photo of the product and its packaging.
- A photo showing the colour or prescription information.
- The batch number or expiry date.
- A short video where this would help clarify the issue.

Requesting this information is intended to help speed up the review of the complaint and does not limit the customer\'s legal rights.',
				'title_ar' => 'كيفية تقديم طلب استبدال أو استرجاع',
				'body_ar'  => 'يمكنك التواصل معنا من خلال:

خدمة العملاء والشكاوى وواتساب:

01060760098

يرجى تزويدنا بـ:

- رقم الطلب أو رقم الهاتف المستخدم في الشراء.
- شرح المشكلة.

وقد نطلب، بحسب الحالة:

- صورة للمنتج والعبوة.
- صورة توضح بيانات اللون أو الدرجة.
- رقم التشغيلة أو تاريخ الصلاحية.
- فيديو قصير إذا كان ذلك يساعد على توضيح المشكلة.

طلب هذه البيانات يهدف إلى تسريع فحص الشكوى، ولا ينتقص من الحقوق القانونية للعميل.',
			),
			array(
				'num'      => '09',
				'title_en' => 'PROOF OF PURCHASE',
				'body_en'  => 'We may request information that helps establish the purchase, such as:

- The order number.
- The registered phone number.
- An invoice or receipt, where available.
- Any other information that enables us to identify the order in our records.

A specific document is not required where the transaction can be established through another method accepted by law.',
				'title_ar' => 'إثبات الشراء',
				'body_ar'  => 'قد نطلب ما يساعد على إثبات عملية الشراء، مثل:

- رقم الطلب.
- رقم الهاتف المسجل.
- الفاتورة أو الإيصال إن وجد.
- أي بيانات أخرى تمكننا من التعرف على الطلب في سجلاتنا.

ولا يشترط تقديم مستند معين إذا كان من الممكن إثبات المعاملة بطريقة أخرى مقبولة وفقًا للقانون.',
			),
			array(
				'num'      => '10',
				'title_en' => 'REVIEWING A MANUFACTURING DEFECT COMPLAINT',
				'body_en'  => 'When we receive a complaint concerning a suspected manufacturing defect, we review the available information, photographs and the product itself, depending on the nature of the case.

Each case is assessed individually.

If a dispute arises regarding the existence of a defect or the right to an exchange or return, the customer retains the right to refer the matter to the Consumer Protection Agency, which may issue a binding decision in matters falling within its legal jurisdiction.',
				'title_ar' => 'فحص شكوى عيب الصناعة',
				'body_ar'  => 'عند استلام شكوى بشأن عيب صناعة، نقوم بمراجعة البيانات والصور والمنتج بحسب طبيعة الحالة.

ويتم تقييم كل حالة بشكل منفصل وفق ظروفها.

وإذا نشأ خلاف حول وجود عيب أو حق الاستبدال أو الاسترجاع، يظل للعميل الحق في اللجوء إلى جهاز حماية المستهلك، والذي يملك وفق القانون إصدار قرار ملزم في المنازعات الداخلة في اختصاصه.',
			),
			array(
				'num'      => '11',
				'title_en' => 'CUSTOMER OPTIONS WHEN A DEFECT EXISTS',
				'body_en'  => 'Where the law provides the customer with a right because of a defect or non-conformity, the solution is not necessarily limited to replacing the product.

The customer\'s request will be handled in accordance with their legal rights and, depending on the circumstances, may include:

- Replacing the product with a conforming product.
- Returning the product and receiving a refund of its value.

The customer will not bear any additional cost where the problem is proven to result from a defect in the product or an error by Lily.',
				'title_ar' => 'خيارات العميل عند وجود عيب',
				'body_ar'  => 'في الحالات التي يقرر فيها القانون حق العميل بسبب وجود عيب أو عدم مطابقة، لا يقتصر الحل بالضرورة على استبدال المنتج فقط.

ويتم التعامل مع طلب العميل وفق حقوقه القانونية، بما في ذلك، بحسب الحالة:

- استبدال المنتج بمنتج مطابق.
- إعادة المنتج واسترداد قيمته.

ولا يتحمل العميل تكلفة إضافية إذا ثبت أن المشكلة ترجع إلى عيب في المنتج أو خطأ من جانب ليلي.',
			),
			array(
				'num'      => '12',
				'title_en' => 'REFUNDING THE PRODUCT VALUE',
				'body_en'  => 'Where the customer is legally entitled to a refund, the amount will be returned using an appropriate method that is consistent with the original payment method and the applicable legal rules.

In cases involving a defective product, the Consumer Protection Agency explains that replacement or refund should be provided without additional cost to the consumer and within the legally prescribed period.

Because our available payment methods include cash, InstaPay and electronic wallets, we may contact the customer to coordinate the appropriate refund method in a manner consistent with the original payment method and the available procedures.',
				'title_ar' => 'استرداد قيمة المنتج',
				'body_ar'  => 'إذا كان العميل مستحقًا لاسترداد قيمة المنتج وفقًا للقانون، يتم رد المبلغ بالطريقة المناسبة وبما يتفق مع طريقة الدفع الأصلية والقواعد القانونية المطبقة.

وفي حالات المنتج المعيب، يتم الاستبدال أو رد القيمة دون تكلفة إضافية على المستهلك وخلال المدة القانونية المقررة.

وبما أن وسائل الدفع لدينا تشمل النقد وإنستا باي والمحافظ الإلكترونية، فقد نتواصل مع العميل لتنسيق وسيلة رد المبلغ المناسبة بما يتفق مع طريقة الدفع الأصلية والإجراءات المتاحة.',
			),
			array(
				'num'      => '13',
				'title_en' => 'CASES THAT ARE NOT CONSIDERED MANUFACTURING DEFECTS',
				'body_en'  => 'The following are not, by themselves, considered defects in the product where it is established that they resulted from:

- Misuse by the customer.
- Failure to follow storage instructions.
- Using the product after its expiry date.
- Exceeding the recommended period of use for the lenses.
- Sharing lenses with another person.
- Damage to the lens during insertion or removal as a result of use.
- The customer selecting an unsuitable prescription or size where the prescription or size actually delivered was the one requested.

Each case is assessed according to its individual circumstances.',
				'title_ar' => 'الحالات التي لا تعد عيب صناعة',
				'body_ar'  => 'لا يعتبر عيبًا في المنتج، في حد ذاته، أي ضرر ثبت أنه نتج عن:

- سوء استخدام العميل.
- عدم اتباع تعليمات التخزين.
- استخدام المنتج بعد انتهاء الصلاحية.
- تجاوز مدة استخدام العدسات.
- مشاركة العدسات مع شخص آخر.
- تلف العدسة أثناء التركيب أو الإزالة بسبب الاستخدام.
- اختيار العميل لدرجة أو مقاس غير مناسبين مع تسليم الدرجة أو المقاس الذي طلبه فعلًا.

ويتم تقييم كل حالة بحسب ظروفها.',
			),
			array(
				'num'      => '14',
				'title_en' => 'LENSES THAT DO NOT SUIT THE USER\'S EYES',
				'body_en'  => 'Some users may experience discomfort while wearing a particular type of lens for reasons that are not necessarily caused by a manufacturing defect.

Lily cannot diagnose the cause of pain, redness or irritation.

If any unusual symptom occurs while using the lenses, the customer should stop using them immediately and consult an eye doctor or qualified specialist.

The presence of medical symptoms does not automatically mean that a product defect exists, nor does it automatically mean that no defect exists. A technical complaint concerning the product is assessed independently from any medical evaluation.',
				'title_ar' => 'عدم ملاءمة العدسات للعين',
				'body_ar'  => 'قد يشعر بعض المستخدمين بعدم راحة أثناء ارتداء نوع معين من العدسات لأسباب لا تكون بالضرورة ناتجة عن عيب صناعة.

ولا تستطيع ليلي تشخيص سبب الألم أو الاحمرار أو التهيج.

إذا حدث أي عرض غير معتاد أثناء استخدام العدسات، يجب التوقف عن استخدامها فورًا ومراجعة طبيب عيون أو مختص مؤهل.

وجود أعراض طبية لا يعني تلقائيًا وجود عيب في المنتج، كما لا يعني تلقائيًا عدم وجود عيب؛ ويتم التعامل مع الشكوى الفنية بصورة مستقلة عن التقييم الطبي.',
			),
			array(
				'num'      => '15',
				'title_en' => 'INCORRECT PRODUCT',
				'body_en'  => 'If you receive a product different from the one confirmed in your order, such as a difference in:

- Colour.
- Prescription.
- Size.
- Type.
- Quantity.

Please do not open the original product packaging and contact us as soon as possible.

If the error is confirmed to be on our side, we will bear the full cost of correcting it and the customer will not be charged any additional delivery costs.',
				'title_ar' => 'المنتج الخطأ',
				'body_ar'  => 'إذا وصل إليك منتج مختلف عن المنتج الذي تم تأكيده في الطلب، مثل اختلاف:

- اللون.
- الدرجة.
- المقاس.
- النوع.
- الكمية.

يرجى عدم فتح العبوة الأصلية للمنتج والتواصل معنا في أسرع وقت.

إذا ثبت الخطأ من جانبنا، نتحمل تكلفة تصحيحه بالكامل ولا يتحمل العميل مصاريف توصيل إضافية.',
			),
			array(
				'num'      => '16',
				'title_en' => 'EXCHANGE AND RETURN COSTS',
				'body_en'  => 'If the exchange or return is required because of a defect in the product or an error by Lily, we will bear the costs required by law and the customer will not bear any additional cost.

Where a customer withdraws from the purchase of a non-defective product that remains sealed in its original condition, the rules relating to shipping and return costs for distance contracts and the Shipping & Delivery Policy apply, within the limits permitted by law.

Egyptian Consumer Protection Law provides consumers in distance contracts with a right to withdraw within 14 days from receiving the goods, with the consumer bearing shipping and return costs unless otherwise agreed, subject to the legal exceptions that may apply depending on the nature and condition of the product.',
				'title_ar' => 'مصاريف الاستبدال والاسترجاع',
				'body_ar'  => 'إذا كان سبب الاستبدال أو الاسترجاع عيبًا في المنتج أو خطأً من جانب ليلي، نتحمل المصاريف التي يفرضها القانون ولا يتحمل العميل تكلفة إضافية.

أما في حالات الرجوع عن شراء منتج غير معيب ومغلق بحالته الأصلية، فتطبق قواعد مصروفات الشحن والإعادة المقررة للتعاقد عن بُعد وسياسة الشحن والتوصيل، في الحدود التي يسمح بها القانون.

وينص قانون حماية المستهلك، في التعاقد عن بُعد، على حق المستهلك في الرجوع خلال 14 يومًا من استلام السلعة، مع تحمل المستهلك نفقات الشحن وإعادة المنتج ما لم يتفق على غير ذلك، وتوجد استثناءات لهذا الحق تتعلق بطبيعة المنتج وظروفه.',
			),
			array(
				'num'      => '17',
				'title_en' => 'CANCELLATION BEFORE RECEIVING THE ORDER',
				'body_en'  => 'If the customer wishes to cancel an order before receiving it, the Shipping & Delivery Policy will apply depending on whether the order is still being prepared or has already been sent out with the delivery representative.

This does not limit any right granted to the consumer under the rules governing distance contracts.',
				'title_ar' => 'الإلغاء قبل الاستلام',
				'body_ar'  => 'إذا أراد العميل إلغاء الطلب قبل استلامه، تطبق سياسة الشحن والتوصيل بحسب ما إذا كان الطلب ما زال تحت التجهيز أو خرج بالفعل مع المندوب.

ولا ينتقص ذلك من أي حق مقرر للمستهلك بموجب أحكام التعاقد عن بُعد.',
			),
			array(
				'num'      => '18',
				'title_en' => 'PRODUCTS THAT MAY NOT BE ACCEPTED FOR RETURN AFTER OPENING',
				'body_en'  => 'Because of their hygienic nature, contact lenses whose original packaging seal has been removed or which have been used cannot normally be accepted for return merely because the customer has changed their mind or wishes to change the colour, size or prescription, where the product conforms to the order.

This exception is not used to prevent the customer from exercising any rights they may have where the product is defective or non-conforming.',
				'title_ar' => 'المنتجات التي قد لا تقبل الإرجاع بعد الفتح',
				'body_ar'  => 'نظرًا إلى طبيعتها الصحية، لا يمكن عادة قبول العدسات اللاصقة التي تمت إزالة ختم عبوتها الأصلية أو استخدامها بغرض تغيير الرأي أو تغيير اللون أو المقاس أو الدرجة إذا كان المنتج مطابقًا للطلب.

ولا يستخدم هذا الاستثناء لمنع العميل من ممارسة حقوقه في حالة وجود عيب أو عدم مطابقة.',
			),
			array(
				'num'      => '19',
				'title_en' => 'ISSUES THAT MAY AFFECT USER SAFETY',
				'body_en'  => 'If information indicates that a defect may exist in a product that could affect the health or safety of users, the matter will be handled in accordance with the legally required procedures, including stopping dealings with the product and issuing a report or warning where required.

Egyptian Consumer Protection Law imposes specific obligations on suppliers where they become aware of a defect that may cause harm to the health or safety of consumers.',
				'title_ar' => 'المشكلات التي قد تمس سلامة المستخدمين',
				'body_ar'  => 'إذا ظهر لدينا ما يشير إلى وجود عيب في منتج قد يهدد صحة أو سلامة المستخدمين، يتم التعامل معه وفق الإجراءات القانونية اللازمة، بما في ذلك وقف التعامل على المنتج والإبلاغ أو التحذير عندما يكون ذلك مطلوبًا.

يفرض قانون حماية المستهلك التزامات خاصة على المورد إذا علم بعيب قد يسبب ضررًا لصحة أو سلامة المستهلك.',
			),
			array(
				'num'      => '20',
				'title_en' => 'CONTACT US',
				'body_en'  => 'For any exchange, return request or complaint after receiving your order:

Lily – Original Lily Lenses

Customer Service, Complaints & WhatsApp:

01060760098

Website:

lilylenses.com

Customer Service:

Available throughout the day',
				'title_ar' => 'التواصل معنا',
				'body_ar'  => 'لأي طلب استبدال أو استرجاع أو شكوى بعد الاستلام:

ليلي – Lily Original Lenses

خدمة العملاء والشكاوى وواتساب:

01060760098

الموقع الإلكتروني:

lilylenses.com

الخدمة:

متاحة على مدار اليوم',
			),
		),
	);

	return $content;
}

/**
 * Default Returns settings (empty = use the approved content above).
 *
 * @return array
 */
function lily_returns_settings_defaults() {
	$defaults = array(
		'returns_hero_title'    => '',
		'returns_hero_title_ar' => '',
		'returns_hero_intro'    => '',
		'returns_hero_intro_ar' => '',
	);

	for ( $i = 1; $i <= 20; $i++ ) {
		$defaults[ "returns_{$i}_title" ]    = '';
		$defaults[ "returns_{$i}_title_ar" ] = '';
		$defaults[ "returns_{$i}_body" ]     = '';
		$defaults[ "returns_{$i}_body_ar" ]  = '';
	}

	return $defaults;
}

/**
 * Sanitize Returns settings.
 *
 * @param array $raw Raw submitted values.
 * @return array
 */
function lily_sanitize_returns_settings( array $raw ) {
	$data = array();

	$data['returns_hero_title']    = isset( $raw['returns_hero_title'] ) ? sanitize_text_field( $raw['returns_hero_title'] ) : '';
	$data['returns_hero_title_ar'] = isset( $raw['returns_hero_title_ar'] ) ? sanitize_text_field( $raw['returns_hero_title_ar'] ) : '';
	$data['returns_hero_intro']    = isset( $raw['returns_hero_intro'] ) ? sanitize_textarea_field( $raw['returns_hero_intro'] ) : '';
	$data['returns_hero_intro_ar'] = isset( $raw['returns_hero_intro_ar'] ) ? sanitize_textarea_field( $raw['returns_hero_intro_ar'] ) : '';

	for ( $i = 1; $i <= 20; $i++ ) {
		$data[ "returns_{$i}_title" ]    = isset( $raw[ "returns_{$i}_title" ] ) ? sanitize_text_field( $raw[ "returns_{$i}_title" ] ) : '';
		$data[ "returns_{$i}_title_ar" ] = isset( $raw[ "returns_{$i}_title_ar" ] ) ? sanitize_text_field( $raw[ "returns_{$i}_title_ar" ] ) : '';
		$data[ "returns_{$i}_body" ]     = isset( $raw[ "returns_{$i}_body" ] ) ? sanitize_textarea_field( $raw[ "returns_{$i}_body" ] ) : '';
		$data[ "returns_{$i}_body_ar" ]  = isset( $raw[ "returns_{$i}_body_ar" ] ) ? sanitize_textarea_field( $raw[ "returns_{$i}_body_ar" ] ) : '';
	}

	return wp_parse_args( $data, lily_returns_settings_defaults() );
}

/**
 * Render the Returns Page tab panels on the existing Lily admin page.
 *
 * @param array $settings Current settings.
 */
function lily_render_returns_fields( $settings ) {
	$approved = lily_returns_approved_content();

	echo '<section id="lily-tab-returns" class="lily-admin-panel"><h2>' . esc_html__( 'Returns Page', 'lily' ) . '</h2>';
	echo '<p class="description">' . esc_html__( 'Every field below is optional. Leave anything blank to keep the approved Returns content.', 'lily' ) . '</p>';

	echo '<h3>' . esc_html__( 'Returns Page — English', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown to English visitors.', 'lily' ) . '</p>';
	lily_admin_text_field( $settings, 'returns_hero_title', esc_html__( 'Hero Title', 'lily' ) );
	lily_admin_textarea_field( $settings, 'returns_hero_intro', esc_html__( 'Hero Introduction', 'lily' ) );

	echo '<h3>' . esc_html__( 'Returns Sections — English', 'lily' ) . '</h3>';
	for ( $i = 1; $i <= 20; $i++ ) {
		$fallback = isset( $approved['sections'][ $i - 1 ] ) ? $approved['sections'][ $i - 1 ] : array();
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Section %d', 'lily' ), $i ) ) . ' — ' . esc_html( $fallback['title_en'] ?? '' ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[returns_%1$d_title]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "returns_{$i}_title" ] ?? '' ), esc_attr__( 'Section heading', 'lily' ) );
		printf( '<textarea name="lily_homepage[returns_%1$d_body]" rows="6" placeholder="%2$s">%3$s</textarea>', absint( $i ), esc_attr__( 'Section content (blank line = new paragraph, "- " = bullet)', 'lily' ), esc_textarea( $settings[ "returns_{$i}_body" ] ?? '' ) );
		echo '</fieldset>';
	}

	echo '<h3>' . esc_html__( 'Returns Page', 'lily' ) . ' — ' . esc_html__( 'العربية', 'lily' ) . '</h3>';
	echo '<p class="description">' . esc_html__( 'Shown on Arabic pages only. Leave any field blank to reuse its English text.', 'lily' ) . '</p>';
	lily_admin_text_field( $settings, 'returns_hero_title_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Hero Title', 'lily' ) ) );
	lily_admin_textarea_field( $settings, 'returns_hero_intro_ar', sprintf( __( '%s (Arabic)', 'lily' ), esc_html__( 'Hero Introduction', 'lily' ) ) );

	echo '<h3>' . esc_html__( 'Returns Sections', 'lily' ) . ' — ' . esc_html__( 'العربية', 'lily' ) . '</h3>';
	for ( $i = 1; $i <= 20; $i++ ) {
		$fallback = isset( $approved['sections'][ $i - 1 ] ) ? $approved['sections'][ $i - 1 ] : array();
		echo '<fieldset class="lily-link-field"><legend>' . esc_html( sprintf( __( 'Section %d (Arabic)', 'lily' ), $i ) ) . ' — ' . esc_html( $fallback['title_ar'] ?? '' ) . '</legend>';
		printf( '<input type="text" name="lily_homepage[returns_%1$d_title_ar]" value="%2$s" placeholder="%3$s">', absint( $i ), esc_attr( $settings[ "returns_{$i}_title_ar" ] ?? '' ), esc_attr__( 'Section heading (Arabic)', 'lily' ) );
		printf( '<textarea name="lily_homepage[returns_%1$d_body_ar]" rows="6" placeholder="%2$s">%3$s</textarea>', absint( $i ), esc_attr__( 'Section content (Arabic)', 'lily' ), esc_textarea( $settings[ "returns_{$i}_body_ar" ] ?? '' ) );
		echo '</fieldset>';
	}

	echo '</section>';
}
