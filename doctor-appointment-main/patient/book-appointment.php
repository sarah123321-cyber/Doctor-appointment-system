<div class="booking-form">
    <h3>Book an Appointment</h3>
    <form id="bookingForm" method="post" action="process-booking.php">
        <input type="hidden" name="docid" value="<?php echo $docid; ?>">
        
        <div class="form-group">
            <label for="preferred_date">Preferred Date:</label>
            <input type="date" id="preferred_date" name="preferred_date" required 
                   min="<?php echo date('Y-m-d'); ?>">
        </div>
        
        <div class="form-group">
            <label for="preferred_time">Preferred Time:</label>
            <input type="time" id="preferred_time" name="preferred_time" required>
        </div>
        
        <div class="form-group">
            <label for="duration">Appointment Duration (minutes):</label>
            <select id="duration" name="duration">
                <option value="30">30 minutes</option>
                <option value="45">45 minutes</option>
                <option value="60">60 minutes</option>
            </select>
        </div>
        
        <div id="slotResult" class="alert" style="display: none;"></div>
        
        <div id="availableSlots" class="available-slots" style="display: none;">
            <h4>Available Time Slots</h4>
            <div class="slots-grid"></div>
        </div>
        
        <button type="button" id="findSlotBtn" class="btn btn-primary">Find Available Slot</button>
        <button type="submit" id="bookBtn" class="btn btn-success" style="display: none;">Book Selected Slot</button>
    </form>
</div>

<script>
document.getElementById('findSlotBtn').addEventListener('click', async function() {
    const date = document.getElementById('preferred_date').value;
    const time = document.getElementById('preferred_time').value;
    const duration = document.getElementById('duration').value;
    
    if (!date || !time) {
        alert('Please select both date and time');
        return;
    }
    
    try {
        const response = await fetch('../find_time_slot.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                doctor_id: <?php echo $docid; ?>,
                preferred_date: date,
                preferred_time: time,
                duration: duration
            })
        });
        
        const data = await response.json();
        const resultDiv = document.getElementById('slotResult');
        const availableSlotsDiv = document.getElementById('availableSlots');
        const slotsGrid = availableSlotsDiv.querySelector('.slots-grid');
        const bookBtn = document.getElementById('bookBtn');
        
        resultDiv.style.display = 'block';
        resultDiv.className = 'alert ' + (data.success ? 'alert-success' : 'alert-warning');
        resultDiv.textContent = data.message;
        
        if (data.success) {
            availableSlotsDiv.style.display = 'block';
            slotsGrid.innerHTML = '';
            
            data.available_slots.forEach(slot => {
                const slotElement = document.createElement('div');
                slotElement.className = 'slot-item' + (slot.id === data.slot.id ? ' selected' : '');
                slotElement.innerHTML = `
                    <div class="slot-time">${slot.start_time} - ${slot.end_time}</div>
                    <button type="button" class="btn btn-sm btn-outline-primary select-slot" 
                            data-slot-id="${slot.id}">Select</button>
                `;
                slotsGrid.appendChild(slotElement);
            });
            
            // Add event listeners to slot selection buttons
            document.querySelectorAll('.select-slot').forEach(button => {
                button.addEventListener('click', function() {
                    document.querySelectorAll('.slot-item').forEach(item => {
                        item.classList.remove('selected');
                    });
                    this.parentElement.classList.add('selected');
                    bookBtn.style.display = 'inline-block';
                });
            });
        } else {
            availableSlotsDiv.style.display = 'none';
            bookBtn.style.display = 'none';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('An error occurred while finding available slots');
    }
});
</script>

<style>
.available-slots {
    margin-top: 20px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 5px;
}

.slots-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.slot-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.slot-item.selected {
    border-color: #4CAF50;
    background: #e8f5e9;
}

.slot-time {
    font-weight: 500;
}

.alert {
    padding: 15px;
    margin: 15px 0;
    border-radius: 5px;
}

.alert-success {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.alert-warning {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeeba;
}
</style> 