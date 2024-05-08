
const faqContainer = document.getElementsByClassName('faq_container');

for (i=0; i<faqContainer.length; i++) {
    faqContainer[i].addEventListener('click', function () {
    this.classList.toggle('active');
    const faqLabelToggle = this.getElementsByClassName('faq_label-toggle')[0];
    if (this.classList.contains('active')){
        faqLabelToggle.innerHTML = '-';
    }else{
        faqLabelToggle.innerHTML = '+';
    }
  });
}
