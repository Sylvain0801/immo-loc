window.onload = () => {
  let btnMessageRead = document.querySelectorAll("a.btn-message")
  for(let btn of btnMessageRead) {
    btn.addEventListener("click", function() {
      let xhr = new XMLHttpRequest()
      xhr.open("get", `/message/agent/read/${this.dataset.id}`)
      xhr.addEventListener('readystatechange', function() {
        if(xhr.readyState === 4) {
            if(xhr.status !== 200) {
                alert('Une erreur s\'est produite, veuillez réessayer plus tard')
            } else {
                btn.getAttribute('value') === '1' ? (
                  btn.setAttribute('value', '0'),
                  btn.setAttribute('class', 'icon fa-envelope btn-message')
                  ) : (
                  btn.setAttribute('value', '1'),
                  btn.setAttribute('class', 'icon fa-envelope-open-o btn-message')
                  )
            }
        }
      })
      xhr.send()
    })
  }
}