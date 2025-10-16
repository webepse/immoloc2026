const addImage = document.querySelector('#add-image')
addImage.addEventListener('click',()=>{
    // compter combien j'ai de form-group pour les indices ex: annonce_image_0_url
    const widgetCounter = document.querySelector("#widgets-counter")
    const index = +widgetCounter.value // le + permet de transformer un string en number -> pcq value return tjrs un string
    const annonceImages = document.querySelector("#annonce_images")
    // recup du prototype dans la div (data-prototype)
    // le drapeau g indique que l'on va le faire plusieurs fois
    const prototype = annonceImages.dataset.prototype.replace(/__name__/g, index)
    // injecter le code dans la div
    annonceImages.insertAdjacentHTML('beforeend', prototype)
    widgetCounter.value = index+1
    // ajouter à la liste pour supprimer
    handleDeleteButtons() // metre à jour le tableau deletes et ajouter l'event
})

const updateCounter = () => {
    const count = document.querySelectorAll('#annonce_images div.form-group').length
    document.querySelector('#widgets-counter').value = count
}

const handleDeleteButtons = () => {
    let deletes = document.querySelectorAll('button[data-action="delete"]')
    deletes.forEach(button => {
        button.addEventListener('click',()=>{
            const target = button.dataset.target
            const elementTarget = document.querySelector(target)
            // pour s'assurer que #block_id existe sinon risque d'erreur
            if(elementTarget){
                elementTarget.remove() // supprimer l'élément
            }
        })
    })
}

updateCounter()
handleDeleteButtons()