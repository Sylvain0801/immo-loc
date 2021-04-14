window.onload = () => {
  // gestion annonces
  let btnDeleteAnnounce = document.querySelectorAll("a.btn-delete-announce")
  for(let btn of btnDeleteAnnounce) {
    btn.addEventListener("click", function() {
      let choice = confirm(this.dataset.confirm)
      if(choice) {
        let xhr = new XMLHttpRequest()
        xhr.open("get", `/announce/delete/${this.dataset.id}`)
        xhr.addEventListener('readystatechange', function() {
          if(xhr.readyState === 4) {
            if(xhr.status !== 200) {
                alert('Une erreur s\'est produite, veuillez réessayer plus tard')
            } else {
                btn.parentNode.parentNode.remove()
            }
          }
        })
        xhr.send()
      }
    })
  }
  let btnActive = document.querySelectorAll("[type=checkbox].btn-active")
  for(let btn of btnActive) {
    btn.addEventListener("click", function() {
      let xhr = new XMLHttpRequest
      xhr.open("get", `/announce/active/${this.dataset.id}`)
      xhr.addEventListener('readystatechange', function() {
        if(xhr.readyState === 4) {
          if(xhr.status !== 200) {
            alert('Une erreur s\'est produite, veuillez réessayer plus tard !')
          }
        }
      })
      xhr.send()
    })
  }
  let btnFirstpage = document.querySelectorAll("[type=checkbox].btn-firstpage")
  for(let btn of btnFirstpage) {
    btn.addEventListener("click", function() {
      let xhr = new XMLHttpRequest
      xhr.open("get", `/announce/firstpage/${this.dataset.id}`)
      xhr.addEventListener('readystatechange', function() {
        if(xhr.readyState === 4) {
          //Si le status n'est pas 200 (HTTP.OK), on alerte l'utilisateur.
          if(xhr.status !== 200) {
            alert('Une erreur s\'est produite, veuillez réessayer plus tard !')
          } 
        }
      })
      xhr.send()
    })
  }
  // Gestion messages
  let btnMessageRead = document.querySelectorAll("a.btn-message")
  for(let btn of btnMessageRead) {
    btn.addEventListener("click", function() {
      let xhr = new XMLHttpRequest()
      xhr.open("get", `/message/read/${this.dataset.id}`)
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
  let btnDeletMessage = document.querySelectorAll("a.btn-delete-message")
  for(let btn of btnDeletMessage) {
    btn.addEventListener("click", function() {
      let choice = confirm(this.dataset.confirm)
      if(choice) {
        let xhr = new XMLHttpRequest()
        xhr.open("get", `/message/delete/${this.dataset.id}`)
        xhr.addEventListener('readystatechange', function() {
          if(xhr.readyState === 4) {
              if(xhr.status !== 200) {
                  alert('Une erreur s\'est produite, veuillez réessayer plus tard')
              } else {
                  btn.parentNode.parentNode.remove()
              }
          }
        })
        xhr.send()
      }
    })
  }
}