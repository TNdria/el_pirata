export default defineNuxtPlugin(() => {

    return {
        provide: {
            dragElement: (elmnt, handle = null) => {
                let pos1 = 0, pos2 = 0, pos3 = 0, pos4 = 0
                const dragTarget = handle || elmnt

                dragTarget.onmousedown = dragMouseDown

                function dragMouseDown(e) {
                    // Empêche le drag si clic dans la zone de redimensionnement (ex: 16x16 px en bas à droite)
                    const rect = elmnt.getBoundingClientRect()
                    const resizeZoneSize = 16
                    const isInResizeZone =
                        e.clientX >= rect.right - resizeZoneSize &&
                        e.clientY >= rect.bottom - resizeZoneSize

                    if (isInResizeZone) {
                        return // Ne pas drag si clic dans coin resize
                    }

                    e.preventDefault()
                    pos3 = e.clientX
                    pos4 = e.clientY
                    document.onmouseup = closeDragElement
                    document.onmousemove = elementDrag
                }

                function elementDrag(e) {
                    e.preventDefault()
                    pos1 = pos3 - e.clientX
                    pos2 = pos4 - e.clientY
                    pos3 = e.clientX
                    pos4 = e.clientY
                    elmnt.style.top = (elmnt.offsetTop - pos2) + "px"
                    elmnt.style.left = (elmnt.offsetLeft - pos1) + "px"
                }

                function closeDragElement() {
                    document.onmouseup = null
                    document.onmousemove = null
                }
            },
            DragMouving: (event) => {
                const element = event.target;
                const tagName = element.tagName.toLowerCase(); // img, audio, video

                if (tagName === 'img' || tagName === 'audio' || tagName === 'video') {
                    const src = element.getAttribute('src');
                    const type = tagName; // pour savoir quoi insérer à l'arrivée

                    event.dataTransfer.setData('media-type', type);
                    event.dataTransfer.setData('media-src', src);
                    console.log('DragMouving:', type, src);
                }

            },
            DragSelectedText: (event) => {
                const selection = window.getSelection();

                if (selection && selection.rangeCount > 0) {
                    const range = selection.getRangeAt(0);
                    const container = document.getElementById('DescriptionEnigme');

                    if (container && container.contains(range.commonAncestorContainer)) {
                        const selectedText = selection.toString().trim();

                        if (selectedText.length > 0) {
                            console.log("Texte sélectionné dans #DescriptionEnigme :", selectedText);
                            event.dataTransfer.setData('media-type', 'texte');
                            event.dataTransfer.setData('media-src', selectedText); // ici c'est le texte sélectionné
                        }
                    }
                }
            },
            DragDroping: (event, page) => {
                event.preventDefault();

                const type = event.dataTransfer.getData('media-type');
                const src = event.dataTransfer.getData('media-src');

                if (!type || !src) return;

                let mediaElement;

                if (type === 'img') {
                    mediaElement = document.createElement('img');
                    mediaElement.className = 'cursor-pointer w-[50px] md:w-[75px]';
                } else if (type === 'audio') {
                    mediaElement = document.createElement('audio');
                    mediaElement.className = 'cursor-pointer w-[50px] md:w-[75px]';
                    mediaElement.controls = true;
                } else if (type === 'video') {
                    mediaElement = document.createElement('video');
                    mediaElement.controls = true;
                    mediaElement.style.width = '200px';
                    mediaElement.className = 'cursor-pointer w-[50px] md:w-[75px]';
                }
                else if (type === 'texte') {
                    mediaElement = document.createElement('p');
                    mediaElement.contentEditable = true;
                    mediaElement.textContent = src || 'Texte modifiable...';
                } else {
                    return;
                }

                mediaElement.src = src;
                mediaElement.draggable = false;
                mediaElement.style.opacity = 0.8;

                // Créer une div contenant le média + textarea
                const container = document.createElement('div');
                container.className = 'd-flex text-center gap-1 w-full';
                container.id = 'canRemove';
                // petit espacement entre l'élément média et le textarea


                // Create the trash icon
                const trashIcon = document.createElement('i');
                trashIcon.className = 'fa fa-trash text-red-900 text-sm rigth-0'; // FontAwesome trash icon
                trashIcon.style.cursor = 'pointer';
                trashIcon.onclick = function () {
                    container.remove(); // Remove the container when the trash icon is clicked
                };

                // Créer le textarea
                const textarea = document.createElement('textarea');
                textarea.rows = 3;
                textarea.cols = 30;
                textarea.maxLength = 75;
                textarea.className = "text-xs p-1 bg-transparent focus:outline-none border-none resize-none text-black";
                textarea.placeholder = "Ajouter une note...";
                textarea.name = "noteText";

                textarea.onclick = function () {
                    textarea.focus()
                };
                // Ajouter les éléments dans le conteneur
                container.appendChild(trashIcon);
                container.appendChild(mediaElement);
                container.appendChild(textarea);

                const ElementTarget = document.getElementById('addNote' + page);

                if (ElementTarget) {
                    // Vérifie si l'élément est déjà en bas du parent
                    const parent = ElementTarget.parentElement;
                    const parentRect = parent.getBoundingClientRect();
                    const elementRect = ElementTarget.getBoundingClientRect();

                    const isAtBottom = elementRect.bottom >= parentRect.bottom - 1;

                    if (isAtBottom) {
                        alert("La page est pleine. Veuillez ajouter une nouvelle page.");
                        return; // on arrête l'insertion
                    }

                    // Si pas plein, insérer le contenu
                    ElementTarget.before(container);

                    // Scroll vers le bas (optionnel)
                    parent.scrollTo({
                        top: parent.scrollHeight,
                        behavior: 'smooth'
                    });
                } else {
                    console.error('Aucun trouvé autour de l\'élément cliqué');
                }
            },
            addTextarea: (event) => {
                // Créer le conteneur et le textarea
                const container = document.createElement('div');
                container.className = 'd-flex text-center mt-1 w-full';
                container.id = 'canRemove';

                const textarea = document.createElement('textarea');
                textarea.rows = 2;
                textarea.cols = 30;
                textarea.maxLength = 75;
                textarea.className = "text-xs p-1 bg-transparent focus:outline-none resize-none border-none text-black";
                textarea.placeholder = "Ajouter une note...";

                const trashIcon = document.createElement('i');
                trashIcon.className = 'fa fa-trash text-red-900 text-sm left-0'; // FontAwesome trash icon
                trashIcon.style.cursor = 'pointer';
                trashIcon.onclick = function () {
                    container.remove(); // Remove the container when the trash icon is clicked
                };

                container.appendChild(trashIcon);
                container.appendChild(textarea);

                // Trouver l'élément <p> qui a été cliqué
                const ElementTarget = event.target.closest('p');

                textarea.onclick = function () {
                    textarea.focus()
                };

                if (ElementTarget) {
                    // Vérifie si l'élément est déjà en bas du parent
                    const parent = ElementTarget.parentElement;
                    const parentRect = parent.getBoundingClientRect();
                    const elementRect = ElementTarget.getBoundingClientRect();

                    const isAtBottom = elementRect.bottom >= parentRect.bottom - 1;

                    if (isAtBottom) {
                        alert("La page est pleine. Veuillez ajouter une nouvelle page.");
                        return; // on arrête l'insertion
                    }

                    // Si pas plein, insérer le contenu
                    ElementTarget.before(container);

                    // Scroll vers le bas (optionnel)
                    parent.scrollTo({
                        top: parent.scrollHeight,
                        behavior: 'smooth'
                    });
                } else {
                    console.error('Aucun trouvé autour de l\'élément cliqué');
                }
            }
        }
    }
})