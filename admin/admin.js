'use strict';
const aside = document.querySelector('aside'), main = document.querySelector('main'), header = document.querySelector('header');
const asideStyle = window.getComputedStyle(aside);
if (localStorage.getItem('admin_menu') == 'closed') {
    aside.classList.add('closed', 'responsive-hidden');
    main.classList.add('full');
    header.classList.add('full');
}
document.querySelector('.responsive-toggle').onclick = event => {
    event.preventDefault();
    if (asideStyle.display == 'none') {
        aside.classList.remove('closed', 'responsive-hidden');
        main.classList.remove('full');
        header.classList.remove('full');
        localStorage.setItem('admin_menu', '');
    } else {
        aside.classList.add('closed', 'responsive-hidden');
        main.classList.add('full');
        header.classList.add('full');
        localStorage.setItem('admin_menu', 'closed');
    }
};
document.querySelectorAll('.tabs a').forEach((element, index) => {
    element.onclick = event => {
        event.preventDefault();
        document.querySelectorAll('.tabs a').forEach(element => element.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach((element2, index2) => {
            if (index == index2) {
                element.classList.add('active');
                element2.style.display = 'block';
            } else {
                element2.style.display = 'none';
            }
        });
    };
});
if (document.querySelector('.filters a')) {
    let filtersList = document.querySelector('.filters .list');
    let filtersListStyle = window.getComputedStyle(filtersList);
    document.querySelector('.filters a').onclick = event => {
        event.preventDefault();
        if (filtersListStyle.display == 'none') {
            filtersList.style.display = 'flex';
        } else {
            filtersList.style.display = 'none';
        }
    };
    document.onclick = event => {
        if (!event.target.closest('.filters')) {
            filtersList.style.display = 'none';
        }
    };
}
document.querySelectorAll('.msg').forEach(element => {
    element.querySelector('.fa-times').onclick = () => {
        element.remove();
        history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]success_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
        history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]error_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
    };
});
if (location.search.includes('success_msg') || location.search.includes('error_msg')) {
    history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]success_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
    history.replaceState && history.replaceState(null, '', location.pathname + location.search.replace(/[\?&]error_msg=[^&]+/, '').replace(/^&/, '?') + location.hash);
}
document.body.addEventListener('click', event => {
    if (!event.target.closest('.multiselect')) {
        document.querySelectorAll('.multiselect .list').forEach(element => element.style.display = 'none');
    } 
});
document.querySelectorAll('.multiselect').forEach(element => {
    let updateList = () => {
        element.querySelectorAll('.item').forEach(item => {
            element.querySelectorAll('.list span').forEach(newItem => {
                if (item.dataset.value == newItem.dataset.value) {
                    newItem.style.display = 'none';
                }
            });
            item.querySelector('.remove').onclick = () => {
                element.querySelector('.list span[data-value="' + item.dataset.value + '"]').style.display = 'flex';
                item.querySelector('.remove').parentElement.remove();
            };
        });
        if (element.querySelectorAll('.item').length > 0) {
            element.querySelector('.search').placeholder = '';
        }
    };
    element.onclick = () => element.querySelector('.search').focus();
    element.querySelector('.search').onfocus = () => element.querySelector('.list').style.display = 'flex';
    element.querySelector('.search').onclick = () => element.querySelector('.list').style.display = 'flex';
    element.querySelector('.search').onkeyup = () => {
        element.querySelector('.list').style.display = 'flex';
        element.querySelectorAll('.list span').forEach(item => {
            item.style.display = item.innerText.toLowerCase().includes(element.querySelector('.search').value.toLowerCase()) ? 'flex' : 'none';
        });
        updateList();
    };
    element.querySelectorAll('.list span').forEach(item => item.onclick = () => {
        item.style.display = 'none';
        let html = `
            <span class="item" data-value="${item.dataset.value}">
                <i class="remove">&times;</i>${item.innerText}
                <input type="hidden" name="${element.dataset.name}" value="${item.dataset.value}">    
            </span>
        `;
        if (element.querySelector('.item')) {
            let ele = element.querySelectorAll('.item');
            ele = ele[ele.length-1];
            ele.insertAdjacentHTML('afterend', html);                          
        } else {
            element.insertAdjacentHTML('afterbegin', html);
        }
        element.querySelector('.search').value = '';
        updateList();
    });
    updateList();
});
const modal = options => {
    let element;
    if (document.querySelector(options.element)) {
        element = document.querySelector(options.element);
    } else if (options.modalTemplate) {
        document.body.insertAdjacentHTML('beforeend', options.modalTemplate());
        element = document.body.lastElementChild;
    }
    options.element = element;
    options.open = obj => {
        element.style.display = 'flex';
        element.getBoundingClientRect();
        element.classList.add('open');
        if (options.onOpen) options.onOpen(obj);
    };
    options.close = obj => {
        if (options.onClose) {
            let returnCloseValue = options.onClose(obj);
            if (returnCloseValue !== false) {
                element.style.display = 'none';
                element.classList.remove('open');
                element.remove();
            }
        } else {
            element.style.display = 'none';
            element.classList.remove('open');
            element.remove();
        }
    };
    if (options.state == 'close') {
        options.close({ source: element, button: null });
    } else if (options.state == 'open') {
        options.open({ source: element }); 
    }
    element.querySelectorAll('.dialog-close').forEach(e => {
        e.onclick = event => {
            event.preventDefault();
            options.close({ source: element, button: e });
        };
    });
    return options;
};
const openMediaLibrary = options => modal({
    media: [],
    state: 'open',
    modalTemplate: function() {
        return `
        <div class="dialog large media-library-modal">
            <div class="content">
                <h3 class="heading">Media Library<span class="dialog-close">&times;</span></h3>
                <div class="media">
                    <div class="list">
                        <div class="list-header">
                            <a href="#" class="btn small green upload-media">Upload</a>
                            <input class="search-media" type="text" placeholder="Search...">
                        </div>
                        <div class="loader"></div>
                    </div>
                    <div class="details">
                        <p>No media selected.</p>
                    </div>
                </div>
                <div class="footer pad-5">
                    <a href="#" class="btn dialog-close save">${options.buttonText ? options.buttonText : 'Save'}</a>
                </div>
            </div>
        </div>
        `;
    },
    detailsTemplate: function(media, img) {
        return `
        <form class="media-details-form" method="post" action="index.php?page=api&action=media&id=${media.id}">
            <h3>Media Details</h3>
            <a href="${img}" target="_blank"><img src="${img}" alt="${media.caption}"></a>
            <label for="title">Title</label>
            <input id="title" type="text" name="title" value="${media.title}">
            <label for="caption">Caption</label>
            <input id="caption" type="text" name="caption" value="${media.caption}">
            <label for="full_path">Full Path</label>
            <input id="full_path" type="text" name="full_path" value="${media.full_path}">
            <label for="date_uploaded">Date Uploaded</label>
            <input id="date_uploaded" type="datetime-local" name="date_uploaded" value="${media.date_uploaded.replace(' ', 'T')}">
            <div class="media-links">
                <a href="#" class="link1 save-media">Save</a> <a href="#" class="link2 delete-media">Delete</a>
            </div>
        </form>
        `;
    },
    selectMedia: function(id) {
        for (let i = 0; i < this.media.length; i++) {
            if (this.media[i].id == id) {
                this.media[i].selected = true;
                this.media[i].element.classList.add('selected');
            }
        }
    },
    unselectMedia: function(id) {
        for (let i = 0; i < this.media.length; i++) {
            if (this.media[i].id == id) {
                this.media[i].selected = false;
                this.media[i].element.classList.remove('selected');
            }
        }
    },
    getAllSelectedMedia: function() {
        return this.media.filter(media => media.selected);
    },
    populateMedia: function(data) {
        data = data ? data : this.media;
        if (this.media.length > 0) {
            document.querySelectorAll('.media-library-modal a.media-image').forEach(element => element.remove());
        }
        for (let i = 0; i < data.length; i++) {
            let img = document.createElement('img');
            img.loading = 'lazy';
            img.src = '../' + data[i].full_path;
            let a = document.createElement('a');
            a.classList.add('media-image');
            a.dataset.index = i;
            a.dataset.id = data[i].id;
            a.append(img);
            a.onclick = event => {
                event.preventDefault();
                a.classList.toggle('selected');
                if (a.classList.contains('selected')) {
                    this.selectMedia(data[i].id);
                    document.querySelector('.media-library-modal .media .details').innerHTML = this.detailsTemplate(data[i], img.src);
                } else if (document.querySelector('.media-library-modal a.media-image.selected')) {
                    this.unselectMedia(data[i].id);
                    let selectedMedia = document.querySelector('.media-library-modal a.media-image.selected');
                    document.querySelector('.media-library-modal .media .details').innerHTML = this.detailsTemplate(data[selectedMedia.dataset.index], selectedMedia.querySelector('img').src);
                } else {
                    this.unselectMedia(data[i].id);
                    document.querySelector('.media-library-modal .media .details').innerHTML = `<p>No media selected.</p>`;
                }
                document.querySelectorAll('.media-library-modal .media .details input').forEach(element => element.onkeyup = () => document.querySelector('.media-library-modal .save-media').style.display = 'inline-flex');
                if (document.querySelector('.media-library-modal .save-media')) {
                    let mediaDetailsForm = document.querySelector('.media-library-modal .media-details-form');
                    document.querySelector('.media-library-modal .save-media').onclick = event => {
                        event.preventDefault();
                        fetch(mediaDetailsForm.action, {
                            method: 'POST',
                            body: new FormData(mediaDetailsForm)
                        }).then(response => response.json()).then(newData => {
                            this.media[i].title = newData[i].title;
                            this.media[i].caption = newData[i].caption;
                            this.media[i].full_path = newData[i].full_path;
                            this.media[i].date_uploaded = newData[i].date_uploaded;
                            data[i].title = newData[i].title;
                            data[i].caption = newData[i].caption;
                            data[i].full_path = newData[i].full_path;
                            data[i].date_uploaded = newData[i].date_uploaded;
                            document.querySelector('.media-library-modal .save-media').style.display = 'none';
                        });
                    };
                    document.querySelector('.media-library-modal .delete-media').onclick = event => {
                        event.preventDefault();
                        if (confirm('Are you sure you want to delete this media?')) {
                            fetch(mediaDetailsForm.action + '&delete=true').then(response => response.json()).then(newData => {
                                for (let j = 0; j < this.media.length; j++) {
                                    for (let k = 0; k < newData.length; k++) {
                                        if (this.media[j].id == newData[k].id && this.media[j].selected) {
                                            newData[k].selected = true;
                                        }
                                    }
                                }
                                this.media = newData;
                                document.querySelector('.media-library-modal .media .details').innerHTML = `<p>No media selected.</p>`;
                                this.populateMedia();
                            });                                
                        }
                    };
                }
            };
            data[i].element = a; 
            document.querySelector('.media-library-modal .media .list').append(a);
        }
        this.getAllSelectedMedia().forEach(media => {
            if (media.selected) this.selectMedia(media.id);
        });
        if (document.querySelector('.media-library-modal .media .loader')) {
            document.querySelector('.media-library-modal .media .loader').remove();
        }
    },
    onOpen: function() {
        fetch('index.php?page=api&action=media', { cache: 'no-store' }).then(response => response.json()).then(data => {
            this.media = data; 
            this.populateMedia();
            if (options.onMediaLoad) options.onMediaLoad();
        });
        document.querySelector('.media-library-modal .upload-media').onclick = event => {
            event.preventDefault();
            let input = document.createElement('input');
            input.type = 'file';
            input.multiple = 'multiple';
            input.accept = 'image/*';
            input.onchange = event => { 
                document.querySelector('.media-library-modal .upload-media').innerHTML = '<div class="loader"></div>';
                let form = new FormData();
                for (let i = 0; i < event.target.files.length; i++) {
                    form.append('file_' + i, event.target.files[i]);
                }
                form.append('total_files', event.target.files.length);
                fetch('index.php?page=api&action=media', {
                    method: 'POST',
                    body: form
                }).then(response => response.json()).then(data => {
                    for (let j = 0; j < this.media.length; j++) {
                        for (let k = 0; k < data.length; k++) {
                            if (this.media[j].id == data[k].id && this.media[j].selected) {
                                data[k].selected = true;
                            }
                        }
                    }
                    this.media = data; 
                    this.populateMedia();
                    document.querySelector('.media-library-modal .upload-media').innerHTML = 'Upload';
                });
            };
            input.click();
        };
        document.querySelector('.media-library-modal .search-media').onchange = () => {
            document.querySelector('.media-library-modal .media .details').innerHTML = `<p>No media selected.</p>`;
            this.populateMedia(this.media.filter(media => {
                return media.title.toLowerCase().includes(document.querySelector('.media-library-modal .search-media').value.toLowerCase())
                        || media.caption.toLowerCase().includes(document.querySelector('.media-library-modal .search-media').value.toLowerCase());
            }));
            this.getAllSelectedMedia().forEach(media => {
                if (media.selected) this.selectMedia(media.id);
            });
        };
    },
    onClose: function(event) {
        if (options.onSave && event && event.button && event.button.classList.contains('save')) options.onSave(this.getAllSelectedMedia());
        if (options.onClose) options.onClose();
    }
});
const openOptionsModal = options => modal({
    state: 'open',
    selectedOptionContainer: null,
    selectedOptionType: null,
    modalTemplate: function() {
        return `
        <div class="dialog medium options-modal">
            <div class="content">
                <h3 class="heading">Add Option<span class="dialog-close">&times;</span></h3>
                <div class="body">
                    <div class="option-header">
                        <input type="text" class="option-title" placeholder="Title" data-default-title="">
                        <select class="option-type">
                            <option value="" disabled selected>-- select type --</option>
                            <option value="0">Select</option>
                            <option value="1">Radio</option>
                            <option value="2">Checkbox</option>
                            <option value="3">Text</option>
                            <option value="4">Date & Time</option>
                        </select>
                        <label>
                            <input type="checkbox" class="option-required">Required
                        </label>
                    </div>
                    ${this.optionTemplate('select')}
                    ${this.optionTemplate('radio')}
                    ${this.optionTemplate('checkbox')}
                    ${this.optionTemplate('text')}
                    ${this.optionTemplate('datetime')}
                </div>
                <div class="footer pad-5">
                    <a href="#" class="btn dialog-close save">${options.buttonText ? options.buttonText : 'Save'}</a>
                </div>
            </div>
        </div>
        `;
    },
    optionTemplate: function(type) {
        return `
        <div class="option-content" data-type="${type}">
            <div class="table">
                <table>
                    <thead>
                        <tr>
                            ${type == 'text' || type == 'datetime' ? '<td>Default Value</td>' : '<td>Name</td>'}
                            ${type == 'text' || type == 'datetime' ? '' : '<td>Quantity</td>'}
                            <td>Price</td>
                            <td>Weight</td>
                            ${type == 'text' || type == 'datetime' ? '' : '<td></td>'}
                        </tr>
                    </thead>
                    <tbody>
                        ${this.optionValueTemplate(type)}
                    </tbody>
                </table> 
            </div>
            ${type == 'text' || type == 'datetime' ? '' : '<a href="#" class="add-option-value-btn"><i class="fa-solid fa-plus"></i>Add Option Value</a>'}
        </div>           
        `;
    },
    optionValueTemplate: function(type, option) {
        if (type == 'text' || type == 'datetime') {
            return `
            <tr class="option-value">
                <td>
                    ${type == 'text' ? '<input type="text" placeholder="Value" class="name" value="' + (option ? option.name : '') + '">' : '<input type="datetime-local" class="name" value="' + (option ? option.name : '') + '">'}
                    <input type="hidden" class="quantity" value="-1">
                </td>
                <td>
                    <div class="input-group">
                        <select class="modifier price-mod">
                            <option value="add"${option && option.price_modifier == 'add' ? ' selected' : ''}>+</option>
                            <option value="subtract"${option && option.price_modifier == 'subtract' ? ' selected' : ''}>-</option>
                        </select>
                        <input type="number" class="price" placeholder="Price" min="0" step=".01" value="${option ? option.price : ''}">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <select class="modifier weight-mod">
                            <option value="add"${option && option.weight_modifier == 'add' ? ' selected' : ''}>+</option>
                            <option value="subtract"${option && option.weight_modifier == 'subtract' ? ' selected' : ''}>-</option>
                        </select>
                        <input type="number" class="weight" placeholder="Weight" min="0" value="${option ? option.weight : ''}">
                    </div>
                </td>
            </tr>
            `;                
        } else {
            return `
            <tr class="option-value">
                <td><input type="text" placeholder="Name" class="name" value="${option ? option.name : ''}"></td>
                <td><input type="number" placeholder="Quantity" class="quantity" title="-1 = unlimited" value="${option ? option.quantity : ''}"></td>
                <td>
                    <div class="input-group">
                        <select class="modifier price-mod">
                            <option value="add"${option && option.price_modifier == 'add' ? ' selected' : ''}>+</option>
                            <option value="subtract"${option && option.price_modifier == 'subtract' ? ' selected' : ''}>-</option>
                        </select>
                        <input type="number" class="price" placeholder="Price" min="0" step=".01" value="${option ? option.price : ''}">
                    </div>
                </td>
                <td>
                    <div class="input-group">
                        <select class="modifier weight-mod">
                            <option value="add"${option && option.weight_modifier == 'add' ? ' selected' : ''}>+</option>
                            <option value="subtract"${option && option.weight_modifier == 'subtract' ? ' selected' : ''}>-</option>
                        </select>
                        <input type="number" class="weight" placeholder="Weight" min="0" value="${option ? option.weight : ''}">
                    </div>
                </td>
                <td><i class="fa-solid fa-xmark remove-option-value"></i></td>
            </tr>
            `;
        }
    },
    onOpen: function() {
        this.element.querySelector('.option-type').onchange = () => {
            this.element.querySelectorAll('.option-content').forEach(element => element.style.display = 'none');
            this.element.querySelectorAll('.option-content')[this.element.querySelector('.option-type').value].style.display = 'flex';
            this.selectedOptionContainer = this.element.querySelectorAll('.option-content')[this.element.querySelector('.option-type').value];
            this.selectedOptionType = this.selectedOptionContainer.dataset.type;
            if (this.selectedOptionContainer.querySelector('.add-option-value-btn')) {
                this.selectedOptionContainer.querySelector('.add-option-value-btn').onclick = event => {
                    event.preventDefault();
                    this.selectedOptionContainer.querySelector('tbody').insertAdjacentHTML('beforeend', this.optionValueTemplate());
                    this.element.querySelectorAll('.remove-option-value').forEach(element => element.onclick = () => element.closest('.option-value').remove());
                };
            }
        };
        if (options.options && options.options.length > 0) {
            this.element.querySelector('.option-title').value = options.options[0].title;
            this.element.querySelector('.option-title').dataset.defaultTitle = options.options[0].title;
            this.element.querySelector('.option-required').checked = parseInt(options.options[0].required) ? true : false;
            this.element.querySelector('.option-type').value = options.options[0].type.replace('select', '0').replace('radio', '1').replace('checkbox', '2').replace('text', '3').replace('datetime', '4');
            this.element.querySelector('.option-type').onchange();
            this.selectedOptionContainer.querySelector('tbody').innerHTML = '';
            options.options.forEach(option => {
                this.selectedOptionContainer.querySelector('tbody').insertAdjacentHTML('beforeend', this.optionValueTemplate(option.type, option));
            });
            this.element.querySelectorAll('.remove-option-value').forEach(element => element.onclick = () => element.closest('.option-value').remove());
        }
    },
    onClose: function(event) {
        if (options.onSave && event && event.button && event.button.classList.contains('save') && this.selectedOptionType != null) {
            if (!this.element.querySelector('.option-title').value) {
                this.element.querySelector('.option-title').setCustomValidity('Please enter the option title!');
                this.element.querySelector('.option-title').reportValidity();
                return false;
            }
            if (options.reservedTitles.includes(this.element.querySelector('.option-title').value.toLowerCase()) && this.element.querySelector('.option-title').value.toLowerCase() != this.element.querySelector('.option-title').dataset.defaultTitle.toLowerCase()) {
                this.element.querySelector('.option-title').setCustomValidity('Title already exists!');
                this.element.querySelector('.option-title').reportValidity();
                return false;              
            }
            let productOptions = [];
            this.selectedOptionContainer.querySelectorAll('.option-value').forEach(optionValue => {
                productOptions.push({
                    title: this.element.querySelector('.option-title').value,
                    name: optionValue.querySelector('.name').value,
                    quantity: optionValue.querySelector('.quantity').value,
                    price: optionValue.querySelector('.price').value,
                    price_modifier: optionValue.querySelector('.price-mod').value,
                    weight: optionValue.querySelector('.weight').value,
                    weight_modifier: optionValue.querySelector('.weight-mod').value,
                    type: this.selectedOptionType,
                    required: this.element.querySelector('.option-required').checked ? 1 : 0
                });
            });
            options.onSave(productOptions);
        }
    }
});
const initProduct = () => {
    let productMediaHandler = () => {
        document.querySelectorAll('.media-position i').forEach(element => element.onclick = event => {
            event.preventDefault();
            let mediaElement = element.closest('.product-media');
            if (element.classList.contains('move-up') && mediaElement.previousElementSibling) {
                mediaElement.parentNode.insertBefore(mediaElement, mediaElement.previousElementSibling);
            }
            if (element.classList.contains('move-down') && mediaElement.nextElementSibling) {
                mediaElement.parentNode.insertBefore(mediaElement.nextElementSibling, mediaElement);
            }
            if (element.classList.contains('media-delete') && confirm('Are you sure you want to delete this media?')) {
                mediaElement.remove();
            }
            document.querySelectorAll('.product-media').forEach((element, index) => {
                element.querySelector('.media-index').innerHTML = index+1;
                element.querySelector('.input-media-position').value = index+1;
            });
        });
    };
    productMediaHandler();
    document.querySelector('.open-media-library-modal').onclick = event => {
        event.preventDefault();
        openMediaLibrary({ 
            multiSelect: true, 
            buttonText: 'Add',
            onSave: function(media) {
                if (media && document.querySelector('.no-images-msg')) {
                    document.querySelector('.no-images-msg').remove();
                }
                media.forEach(m => {
                    let index = document.querySelectorAll('.product-media').length;
                    document.querySelector('.product-media-container').insertAdjacentHTML('beforeend', `
                        <div class="product-media">
                            <span class="media-index responsive-hidden">${index+1}</span>
                            <a class="media-img" href="../${m.full_path}" target="_blank">
                                <img src="../${m.full_path}">
                            </a>
                            <div class="media-text">
                                <h3 class="responsive-hidden">${m.title}</h3>
                                <p class="responsive-hidden">${m.caption}</p>
                            </div>
                            <div class="media-position">
                                <i class="fas fa-times media-delete"></i>
                                <i class="fas fa-arrow-up move-up"></i>
                                <i class="fas fa-arrow-down move-down"></i>
                            </div>
                            <input type="hidden" class="input-media-id" name="media[]" value="${m.id}">
                            <input type="hidden" class="input-media-product-id" name="media_product_id[]" value="0">
                            <input type="hidden" class="input-media-position" name="media_position[]" value="${index+1}">
                        </div>                    
                    `);
                });
                productMediaHandler();
            }
        });
    };
    let productOptionsHandler = () => {
        document.querySelectorAll('.option-position i').forEach(element => element.onclick = event => {
            event.preventDefault();
            let optionElement = element.closest('.product-option');
            if (element.classList.contains('move-up') && optionElement.previousElementSibling) {
                optionElement.parentNode.insertBefore(optionElement, optionElement.previousElementSibling);
            }
            if (element.classList.contains('move-down') && optionElement.nextElementSibling) {
                optionElement.parentNode.insertBefore(optionElement.nextElementSibling, optionElement);
            }
            if (element.classList.contains('option-delete') && confirm('Are you sure you want to delete this option?')) {
                optionElement.remove();
            }
            if (element.classList.contains('option-edit')) {
                let options = [];
                optionElement.querySelectorAll('.input-option-value').forEach(optionValue => {
                    options.push({
                        title: optionValue.querySelector('.input-option-title').value,
                        name: optionValue.querySelector('.input-option-name').value,
                        quantity: optionValue.querySelector('.input-option-quantity').value,
                        price: optionValue.querySelector('.input-option-price').value,
                        price_modifier: optionValue.querySelector('.input-option-price-modifier').value,
                        weight: optionValue.querySelector('.input-option-weight').value,
                        weight_modifier: optionValue.querySelector('.input-option-weight-modifier').value,
                        type: optionValue.querySelector('.input-option-type').value,
                        required: optionValue.querySelector('.input-option-required').value
                    });
                });
                openOptionsModal({ 
                    buttonText: 'Save',
                    reservedTitles: [...document.querySelectorAll('.product-option')].map(option => option.querySelector('.input-option-title').value.toLowerCase()),
                    options: options,
                    onSave: function(options) {
                        if (options.length > 0) {
                            let optionsHtml = '';
                            let optionsValuesHtml = '';
                            options.forEach(option => {
                                optionsHtml += `
                                <div class="input-option-value">
                                    <input type="hidden" class="input-option-title" name="option_title[]" value="${option.title}">
                                    <input type="hidden" class="input-option-name" name="option_name[]" value="${option.name}">
                                    <input type="hidden" class="input-option-quantity" name="option_quantity[]" value="${option.quantity}">
                                    <input type="hidden" class="input-option-price" name="option_price[]" value="${option.price}">
                                    <input type="hidden" class="input-option-price-modifier" name="option_price_modifier[]" value="${option.price_modifier}">
                                    <input type="hidden" class="input-option-weight" name="option_weight[]" value="${option.weight}">
                                    <input type="hidden" class="input-option-weight-modifier" name="option_weight_modifier[]" value="${option.weight_modifier}">
                                    <input type="hidden" class="input-option-type" name="option_type[]" value="${option.type}">
                                    <input type="hidden" class="input-option-required" name="option_required[]" value="${option.required}">
                                    <input type="hidden" class="input-option-position" name="option_position[]" value="${optionElement.querySelector('.input-option-position').value}">
                                </div>                   
                                `;
                                optionsValuesHtml += option.name + ', ';
                            })
                            optionElement.innerHTML = `                    
                                <span class="option-index responsive-hidden">${optionElement.querySelector('.option-index').innerHTML}</span>
                                <div class="option-text">
                                    <h3>${options[0].title} (${options[0].type})</h3>
                                    <p>${optionsValuesHtml.replace(/, $/, '')}</p>
                                </div>
                                <div class="option-position">
                                    <i class="fas fa-pen option-edit"></i>
                                    <i class="fas fa-times option-delete"></i>
                                    <i class="fas fa-arrow-up move-up"></i>
                                    <i class="fas fa-arrow-down move-down"></i>
                                </div>
                                ${optionsHtml}
                            `;
                        }
                        productOptionsHandler();
                    }
                });    
            }
            document.querySelectorAll('.product-option').forEach((element, index) => {
                element.querySelector('.option-index').innerHTML = index+1;
                element.querySelectorAll('.input-option-position').forEach(input => input.value = index+1);
            });
        });
    };
    productOptionsHandler();
    document.querySelector('.open-options-modal').onclick = event => {
        event.preventDefault();
        openOptionsModal({ 
            buttonText: 'Add',
            reservedTitles: [...document.querySelectorAll('.product-option')].map(option => option.querySelector('.input-option-title').value.toLowerCase()),
            onSave: function(options) {
                if (options.length > 0) {
                    if (document.querySelector('.no-options-msg')) {
                        document.querySelector('.no-options-msg').remove();
                    }
                    let index = document.querySelectorAll('.product-option').length;
                    let optionsHtml = '';
                    let optionsValuesHtml = '';
                    options.forEach(option => {
                        optionsHtml += `
                        <div class="input-option-value">
                            <input type="hidden" class="input-option-title" name="option_title[]" value="${option.title}">
                            <input type="hidden" class="input-option-name" name="option_name[]" value="${option.name}">
                            <input type="hidden" class="input-option-quantity" name="option_quantity[]" value="${option.quantity}">
                            <input type="hidden" class="input-option-price" name="option_price[]" value="${option.price}">
                            <input type="hidden" class="input-option-price-modifier" name="option_price_modifier[]" value="${option.price_modifier}">
                            <input type="hidden" class="input-option-weight" name="option_weight[]" value="${option.weight}">
                            <input type="hidden" class="input-option-weight-modifier" name="option_weight_modifier[]" value="${option.weight_modifier}">
                            <input type="hidden" class="input-option-type" name="option_type[]" value="${option.type}">
                            <input type="hidden" class="input-option-required" name="option_required[]" value="${option.required}">
                            <input type="hidden" class="input-option-position" name="option_position[]" value="${index+1}">
                        </div>                   
                        `;
                        optionsValuesHtml += option.name + ', ';
                    })
                    document.querySelector('.product-options-container').insertAdjacentHTML('beforeend', `
                    <div class="product-option">                 
                        <span class="option-index responsive-hidden">${index+1}</span>
                        <div class="option-text">
                            <h3>${options[0].title} (${options[0].type})</h3>
                            <p>${optionsValuesHtml.replace(/, $/, '')}</p>
                        </div>
                        <div class="option-position">
                            <i class="fas fa-pen option-edit"></i>
                            <i class="fas fa-times option-delete"></i>
                            <i class="fas fa-arrow-up move-up"></i>
                            <i class="fas fa-arrow-down move-down"></i>
                        </div>
                        ${optionsHtml}
                    </div>
                    `);
                }
                productOptionsHandler();
            }
        });  
    };
    let productDownloadsHandler = () => {
        document.querySelectorAll('.download-position i').forEach(element => element.onclick = event => {
            event.preventDefault();
            let downloadElement = element.closest('.product-download');
            if (element.classList.contains('move-up') && downloadElement.previousElementSibling) {
                downloadElement.parentNode.insertBefore(downloadElement, downloadElement.previousElementSibling);
            }
            if (element.classList.contains('move-down') && downloadElement.nextElementSibling) {
                downloadElement.parentNode.insertBefore(downloadElement.nextElementSibling, downloadElement);
            }
            if (element.classList.contains('download-delete') && confirm('Are you sure you want to delete this digital download?')) {
                downloadElement.remove();
            }
            document.querySelectorAll('.product-download').forEach((element, index) => {
                element.querySelector('.download-index').innerHTML = index+1;
                element.querySelector('.input-download-position').value = index+1;
            });
        });
    };
    productDownloadsHandler();
    document.querySelector('.open-downloads-modal').onclick = event => {
        event.preventDefault();
        modal({
            state: 'open',
            modalTemplate: function() {
                return `
                <div class="dialog downloads-modal">
                    <div class="content">
                        <h3 class="heading">Add Digital Download<span class="dialog-close">&times;</span></h3>
                        <div class="body">
                            <p>The file path must be relative to the shopping cart root directory.</p>
                            <label for="download-file-path">File Path</label>
                            <input id="download-file-path" type="text" class="download-file-path" placeholder="your_hidden_directory/your_file.zip" data-default-title="">
                            <p class="download-result"></p>
                        </div>
                        <div class="footer pad-5">
                            <a href="#" class="btn dialog-close save disabled">Add</a>
                        </div>
                    </div>
                </div>
                `;
            },
            onOpen: function() {
                this.element.querySelector('.download-file-path').onchange = () => {
                    fetch('index.php?page=api&action=fileexists&path=' + this.element.querySelector('.download-file-path').value, { cache: 'no-store' }).then(response => response.json()).then(data => {
                        this.element.querySelector('.download-result').innerHTML = data.result;
                        if (data.result) {
                            this.element.querySelector('.save').classList.add('disabled');
                        } else {
                            this.element.querySelector('.save').classList.remove('disabled');
                        }
                    });
                };
            },
            onClose: function(event) {
                if (event && event.button && event.button.classList.contains('save')) {
                    if (event.button.classList.contains('disabled')) return false;
                    if (document.querySelector('.no-downloads-msg')) {
                        document.querySelector('.no-downloads-msg').remove();
                    }
                    let index = document.querySelectorAll('.product-download').length;
                    let file_path = document.querySelector('.download-file-path').value;
                    document.querySelector('.product-downloads-container').insertAdjacentHTML('beforeend', `
                        <div class="product-download">
                            <span class="download-index responsive-hidden">${index+1}</span>
                            <div class="download-text">
                                <h3 class="responsive-hidden">${file_path}</h3>
                                <p class="responsive-hidden"></p>
                            </div>
                            <div class="download-position">
                                <i class="fas fa-times download-delete"></i>
                                <i class="fas fa-arrow-up move-up"></i>
                                <i class="fas fa-arrow-down move-down"></i>
                            </div>
                            <input type="hidden" class="input-download-file-path" name="download_file_path[]" value="${file_path}">
                            <input type="hidden" class="input-download-position" name="download_position[]" value="${index+1}">
                        </div>                    
                    `);                                      
                    productDownloadsHandler();
                }
            }
        });        
    };
};
const initMedia = () => {
    let mediaHandler = () => {
        document.querySelectorAll('.media .image').forEach(element => element.onclick = event => {
            event.preventDefault();
            modal({
                state: 'open',
                modalTemplate: function() {
                    return `
                    <div class="dialog edit-media-modal">
                        <div class="content">
                            <h3 class="heading">Edit Media<span class="dialog-close">&times;</span></h3>
                            <div class="body">
                                <form class="media-details-form" method="post" action="index.php?page=api&action=media&id=${element.dataset.id}">
                                    <a href="../${element.dataset.fullPath}" target="_blank"><img src="../${element.dataset.fullPath}" alt=""></a>
                                    <label for="title">Title</label>
                                    <input id="title" type="text" name="title" value="${element.dataset.title}">
                                    <label for="caption">Caption</label>
                                    <input id="caption" type="text" name="caption" value="${element.dataset.caption}">
                                    <label for="full_path">Full Path</label>
                                    <input id="full_path" type="text" name="full_path" value="${element.dataset.fullPath}">
                                    <label for="date_uploaded">Date Uploaded</label>
                                    <input id="date_uploaded" type="datetime-local" name="date_uploaded" value="${element.dataset.dateUploaded}">
                                </form>
                            </div>
                            <div class="footer pad-5">
                                <a href="#" class="btn dialog-close save mar-right-1">Save</a>
                                <a href="#" class="btn dialog-close delete red">Delete</a>
                            </div>
                        </div>
                    </div>
                    `;
                },
                onClose: function(event) {
                    let mediaDetailsForm = this.element.querySelector('.media-details-form');
                    if (event && event.button && event.button.classList.contains('save')) {
                        fetch(mediaDetailsForm.action, {
                            method: 'POST',
                            body: new FormData(mediaDetailsForm)
                        }).then(response => response.json()).then(data => {
                            data.forEach(media => {
                                if (media.id == element.dataset.id) {
                                    element.dataset.title = media.title;
                                    element.dataset.caption = media.caption;
                                    element.dataset.fullPath = media.fullPath;
                                    element.dataset.dateUploaded = media.dateUploaded;
                                }
                            });
                        });                    
                    }
                    if (event && event.button && event.button.classList.contains('delete')) {
                        fetch(mediaDetailsForm.action + '&delete=true').then(response => response.json()).then(() => element.remove());                    
                    }
                }
            });        
        });
    };
    mediaHandler();
    document.querySelector('.upload').onclick = event => {
        event.preventDefault();
        let input = document.createElement('input');
        input.type = 'file';
        input.multiple = 'multiple';
        input.accept = 'image/*';
        input.onchange = event => { 
            document.querySelector('.upload').innerHTML = '<div class="loader"></div>';
            let total_files = event.target.files.length;
            let form = new FormData();
            for (let i = 0; i < total_files; i++) {
                form.append('file_' + i, event.target.files[i]);
            }
            form.append('total_files', total_files);
            fetch('index.php?page=api&action=media', {
                method: 'POST',
                body: form
            }).then(response => response.json()).then(data => {
                if (data) {
                    data.forEach((media, index) => {
                        if (index > total_files-1) return;
                        document.querySelector('.media').insertAdjacentHTML('afterbegin', `
                        <a href="#" class="image" data-id="${media.id}" data-title="${media.title}" data-caption="${media.caption}" data-date-uploaded="${media.date_uploaded.replace(' ', 'T')}" data-full-path="${media.full_path}">
                            <img src="../${media.full_path}" alt="${media.caption}" loading="lazy">
                        </a>
                        `);
                    });
                }
                document.querySelector('.upload').innerHTML = 'Upload';
                mediaHandler();
            });
        };
        input.click();
    }; 
};
const initManageOrder = (products) => {
    document.querySelector('.add-item').onclick = event => {
        event.preventDefault();
        document.querySelector('.manage-order-table tbody').insertAdjacentHTML('beforeend', `
        <tr>
            <td>
                <input type="hidden" name="item_id[]" value="0">
                <select name="item_product[]">
                    ${products.map(product => '<option value="' + product.id + '">' + product.id + ' - ' + product.name + '</option>')}
                </select>
            </td>
            <td><input name="item_price[]" type="number" placeholder="Price" step=".01"></td>
            <td><input name="item_quantity[]" type="number" placeholder="Quantity"></td>
            <td><input name="item_options[]" type="text" placeholder="Options"></td>
            <td><i class="fa-solid fa-xmark delete-item"></i></td>
        </tr>
        `);
        document.querySelectorAll('.delete-item').forEach(element => element.onclick = event => {
            event.preventDefault();
            element.closest('tr').remove();
        });
        if (document.querySelector('.no-order-items-msg')) {
            document.querySelector('.no-order-items-msg').remove();
        }
    };
    document.querySelectorAll('.delete-item').forEach(element => element.onclick = event => {
        event.preventDefault();
        element.closest('tr').remove();
    });
};