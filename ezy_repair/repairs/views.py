from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from .models import Repair, Location, Make, RepairStatus, ActivityLog, TechnicianNotes
from customers.models import Customer
from django.core.paginator import Paginator, EmptyPage, PageNotAnInteger
from django.core.files.base import ContentFile
import base64
from django.conf import settings

from django.views.decorators.csrf import csrf_exempt
from django.core.mail import send_mail
from twilio.rest import Client
from django.template.loader import render_to_string
from django.utils.html import strip_tags
import datetime
# from datetime import datetime
from django.http import JsonResponse
from django.db.models import Q
import json
import random
import string

from .utils import send_email_with_smtp_go


# Repairs
@login_required
def repairs_list(request):
    repairs = Repair.objects.all()
    page = request.GET.get('page', 1)  # Get the current page number from the request
    paginator = Paginator(repairs, 10)  # 10 logs per page

    try:
        repairs = paginator.page(page)
    except PageNotAnInteger:
        repairs = paginator.page(1)  # If page is not an integer, show the first page
    except EmptyPage:
        repairs = paginator.page(paginator.num_pages)  # If page is out of range, show the last page
    statuses = RepairStatus.objects.all()
    customers = Customer.objects.all()
    locations = Location.objects.all()
    mob_yes = ['lens_lcd_damage', 'camera_lens_back_damage', 'risk_back', 'risk_biometric', 'button_function_ok', 'sim_removed', 'risk_lcd']
    lap_yes = ['keyboard_functional', 'screen_damage', 'hinge_damage', 'trackpad_functional']
    mandatory_yes = ['tampered', 'missing_part', 'power_up', 'liquid_damage']
    print("repairs", repairs)
    makes = Make.objects.all()
    return render(request, 'repairs/repairs_list.html', {'repairs': repairs, "statuses": statuses,
                                                         "customers": customers, "locations": locations,
                                                         "makes": makes, "mob_yes": mob_yes, "lap_yes": lap_yes, "mandatory_yes": mandatory_yes})

@login_required
def add_repair(request):
    if request.method == "POST":
        # Handle repair creation logic
        customer_id = request.POST.get("customer")
        device_type = request.POST.get("device_type")
        status_id = request.POST.get("status")
        location_id = request.POST.get("location")
        make_id = request.POST.get("make")
        model = request.POST.get("model")
        issue_description = request.POST.get("issue_description")
        estimated_cost = request.POST.get("estimated_cost")
        finalized_price = request.POST.get("finalized_price")
        passcode = request.POST.get("passcode")
        signature_data = request.POST.get("signatureImage_add")
        current_date = datetime.datetime.now().strftime('%Y-%m-%d')
        liquid_damage = request.POST.get("liquid_damage")
        power_up = request.POST.get("power_up")
        missing_part = request.POST.get("missing_part")
        tampered = request.POST.get("tampered")
        print("signature", signature_data)

        # Generate a random passcode (e.g., a 6-character string of digits)

        # Format the repair number as 'ezy-date-client_name-passcode'

        status = get_object_or_404(RepairStatus, id=status_id) if status_id else None
        location = get_object_or_404(Location, id=location_id) if location_id else None
        make = get_object_or_404(Make, id=make_id) if make_id else None
        customer = get_object_or_404(Customer, id=customer_id)
        repair_number = f"ezy-{current_date}-{customer.first_name}-{passcode}"

        repair = Repair.objects.create(
            repair_number=repair_number,
            passcode=passcode,
            customer=customer,
            device_type=device_type,
            status=status,
            location=location,
            make=make,
            model=model,
            issue_description=issue_description,
            estimated_cost=estimated_cost,
            finalized_price=finalized_price,
            liquid_damage=liquid_damage,
            power_up=power_up,
            missing_part=missing_part,
            tampered=tampered
        )
        if device_type == "Mobile" or "Tablet":
            imei = request.POST.get("imei")
            lens_lcd_damage = request.POST.get("lens_lcd_damage")
            camera_lens_back_damage = request.POST.get("camera_lens_back_damage")
            risk_back = request.POST.get("risk_back")
            risk_biometric = request.POST.get("risk_biometric")
            button_function_ok = request.POST.get("button_function_ok")
            sim_removed = request.POST.get("sim_removed")
            risk_lcd = request.POST.get("risk_lcd")
            network = request.POST.get("network")
            repair.imei = imei
            repair.network = network
            repair.lens_lcd_damage = lens_lcd_damage
            repair.camera_lens_back_damage = camera_lens_back_damage
            repair.risk_back = risk_back
            repair.risk_biometric = risk_biometric
            repair.button_function_ok = button_function_ok
            repair.sim_removed = sim_removed
            repair.risk_lcd = risk_lcd
        if device_type == "Laptop":
            storage = request.POST.get("storage")
            ram = request.POST.get("ram")
            operating_system = request.POST.get("operating_system")
            trackpad_functional = request.POST.get("trackpad_functional")
            keyboard_functional = request.POST.get("keyboard_functional")
            hinge_damage = request.POST.get("hinge_damage")
            screen_damage = request.POST.get("screen_damage")
            repair.trackpad_functional = trackpad_functional
            repair.keyboard_functional = keyboard_functional
            repair.hinge_damage = hinge_damage
            repair.screen_damage = screen_damage
            repair.storage = storage
            repair.ram = ram
            repair.operating_system = operating_system
        repair.save()
        if signature_data:
            format, imgstr = signature_data.split(';base64,')
            ext = format.split('/')[-1]
            repair.signature.save(f"repair_{repair.id}_signature.{ext}", ContentFile(base64.b64decode(imgstr)))
        ActivityLog.objects.create(description=f"Add repair with id {repair.id}", user=request.user)

    return redirect('repairs_list')


@login_required
def add_location(request):
    if request.method == 'POST':
        name = request.POST.get('name')
        address = request.POST.get('address')
        if name and address:
            location = Location.objects.create(name=name, address=address)
            ActivityLog.objects.create(description=f"Add Location name {location.name}.", user=request.user)



    return redirect('locations')


@login_required
def add_make(request):
    if request.method == "POST":
        name = request.POST.get('name')
        if name:
            make = Make.objects.create(name=name)
            ActivityLog.objects.create(description=f"Add make name {make.name}.", user=request.user)
    return redirect('makes')


@login_required
def add_repair_status(request):
    if request.method == "POST":
        # Handle repair creation logic
        status = request.POST.get("name")
        description = request.POST.get("description")
        create_status = RepairStatus.objects.create(name=status, description=description)
        ActivityLog.objects.create(description=f"Add new repair status name {create_status.name}", user=request.user)
        return redirect('repair_statuses')
    return redirect('repair_statuses')


@login_required
def edit_repair_status(request, id):
    status = get_object_or_404(RepairStatus, id=id)
    if request.method == "POST":
        # Update logic here
        status.name = request.POST.get("name")
        status.description = request.POST.get("description")
        status.save()
        ActivityLog.objects.create(description=f"Edit repair status {status.name}", user=request.user)
        return redirect('repair_statuses')  # Replace with the correct redirect
    return render(request, 'repairs/edit_repair_status.html', {'status': status})

@login_required
def edit_location(request, pk):
    status = get_object_or_404(Location, id=pk)
    if request.method == "POST":
        # Update logic here
        status.name = request.POST.get("name")
        status.address = request.POST.get("address")

        status.save()
        ActivityLog.objects.create(description=f"Edit location status {status.name}", user=request.user)
        # return redirect('repairs_list')  # Replace with the correct redirect
    return redirect('locations')

@login_required
def edit_repair_delete(request, id):
    status = get_object_or_404(RepairStatus, id=id)
    if request.method == "POST":
        # Update logic here
        ActivityLog.objects.create(description=f"Delete repair status {status.name}", user=request.user)
        status.delete()
        return redirect('repair_statuses')  # Replace with the correct redirect
    return render(request, 'repairs/edit_repair_status.html')

@login_required
def location_delete(request, pk):
    status = get_object_or_404(Location, id=pk)
    if request.method == "POST":
        # Update logic here
        ActivityLog.objects.create(description=f"Delete location {status.name}", user=request.user)
        status.delete()
        # return redirect('locations')  # Replace with the correct redirect
    return redirect('locations')


@login_required
def edit_repair(request, pk):
    repair = get_object_or_404(Repair, pk=pk)
    if request.method == "POST":
        customer_id = request.POST.get("customer")
        device_type = request.POST.get("device_type")
        status_id = request.POST.get("status")
        location_id = request.POST.get("location")
        make_id = request.POST.get("make")
        model = request.POST.get("model")
        issue_description = request.POST.get("issue_description")
        estimated_cost = request.POST.get("estimated_cost")
        finalized_price = request.POST.get("finalized_price")
        liquid_damage = request.POST.get("liquid_damage")
        power_up = request.POST.get("power_up")
        missing_part = request.POST.get("missing_part")
        tampered = request.POST.get("tampered")
        signature_image_data = request.POST.get("signature_image")  # Getting the base64 signature image data

        print("issue description", issue_description)
        if signature_image_data:
            # Decode the base64 image and save it as an image file

            format, imgstr = signature_image_data.split(';base64,')
            ext = format.split('/')[-1]
            repair.signature.save(f"repair_{repair.id}_signature.{ext}", ContentFile(base64.b64decode(imgstr)))
        customer = get_object_or_404(Customer, id=customer_id)
        status = get_object_or_404(RepairStatus, id=status_id) if status_id else None
        location = get_object_or_404(Location, id=location_id) if location_id else None
        make = get_object_or_404(Make, id=make_id) if make_id else None

        repair.customer = customer
        repair.status = status
        repair.location = location
        repair.make = make
        repair.model = model
        # repair.device_type = device_type
        repair.issue_description = issue_description
        repair.estimated_cost = estimated_cost
        repair.finalized_price = finalized_price
        repair.liquid_damage = liquid_damage
        repair.power_up = power_up
        repair.missing_part = missing_part
        repair.tampered = tampered

        if repair.device_type == "Mobile" or "Tablet":
            imei = request.POST.get("imei")
            lens_lcd_damage = request.POST.get("lens_lcd_damage")
            camera_lens_back_damage = request.POST.get("camera_lens_back_damage")
            risk_back = request.POST.get("risk_back")
            risk_biometric = request.POST.get("risk_biometric")
            button_function_ok = request.POST.get("button_function_ok")
            sim_removed = request.POST.get("sim_removed")
            risk_lcd = request.POST.get("risk_lcd")
            network = request.POST.get("network")
            repair.imei = imei
            repair.network = network
            repair.lens_lcd_damage = lens_lcd_damage
            repair.camera_lens_back_damage = camera_lens_back_damage
            repair.risk_back = risk_back
            repair.risk_biometric = risk_biometric
            repair.button_function_ok = button_function_ok
            repair.sim_removed = sim_removed
            repair.risk_lcd = risk_lcd
        if repair.device_type == "Laptop":
            storage = request.POST.get("storage")
            ram = request.POST.get("ram")
            operating_system = request.POST.get("operating_system")
            trackpad_functional = request.POST.get("trackpad_functional")
            keyboard_functional = request.POST.get("keyboard_functional")
            hinge_damage = request.POST.get("hinge_damage")
            screen_damage = request.POST.get("screen_damage")
            print("aaa", operating_system, trackpad_functional, keyboard_functional, ram, storage)
            repair.trackpad_functional = trackpad_functional
            repair.keyboard_functional = keyboard_functional
            repair.hinge_damage = hinge_damage
            repair.screen_damage = screen_damage
            repair.storage = storage
            repair.ram = ram
            repair.operating_system = operating_system

        repair.save()
        ActivityLog.objects.create(description=f"Edit repair with id {repair.id}", user=request.user)
    return redirect('repairs_list')

@login_required
def delete_repair(request, pk):
    repair = get_object_or_404(Repair, pk=pk)
    ActivityLog.objects.create(description=f"Delete repair with id {repair.id}", user=request.user)
    repair.delete()
    return redirect('repairs_list')

# Locations
@login_required
def locations(request):
    locations = Location.objects.all()
    page = request.GET.get('page', 1)  # Get the current page number from the request
    paginator = Paginator(locations, 10)  # 10 logs per page

    try:
        locations = paginator.page(page)
    except PageNotAnInteger:
        locations = paginator.page(1)  # If page is not an integer, show the first page
    except EmptyPage:
        locations = paginator.page(paginator.num_pages)  # If page is out of range, show the last page
    return render(request, 'repairs/locations.html', {'locations': locations})

# Makes
@login_required
def makes(request):
    makes = Make.objects.all()
    page = request.GET.get('page', 1)  # Get the current page number from the request
    paginator = Paginator(makes, 10)  # 10 logs per page

    try:
        makes = paginator.page(page)
    except PageNotAnInteger:
        makes = paginator.page(1)  # If page is not an integer, show the first page
    except EmptyPage:
        makes = paginator.page(paginator.num_pages)  # If page is out of range, show the last page
    return render(request, 'repairs/makes.html', {'makes': makes})


@login_required
def edit_make(request, pk):
    status = get_object_or_404(Make, id=pk)
    if request.method == "POST":
        # Update logic here
        status.name = request.POST.get("name")

        status.save()
        ActivityLog.objects.create(description=f"Edit make {status.name}", user=request.user)
        # return redirect('repairs_list')  # Replace with the correct redirect
    return redirect('makes')

@login_required
def delete_make(request, pk):
    repair = get_object_or_404(Make, pk=pk)
    ActivityLog.objects.create(description=f"Delete make {repair.name}", user=request.user)
    repair.delete()
    return redirect('makes')


# Repair Statuses
@login_required
def repair_statuses(request):
    statuses = RepairStatus.objects.all()
    page = request.GET.get('page', 1)  # Get the current page number from the request
    paginator = Paginator(statuses, 10)  # 10 logs per page

    try:
        statuses = paginator.page(page)
    except PageNotAnInteger:
        statuses = paginator.page(1)  # If page is not an integer, show the first page
    except EmptyPage:
        statuses = paginator.page(paginator.num_pages)  # If page is out of range, show the last page
    return render(request, 'repairs/repair_statuses.html', {'statuses': statuses})

# Activity Logs
@login_required
def activity_logs(request):
    logs_list = ActivityLog.objects.all().order_by("-created_at")

    # Pagination settings
    page = request.GET.get('page', 1)  # Get the current page number from the request
    paginator = Paginator(logs_list, 10)  # 10 logs per page

    try:
        logs = paginator.page(page)
    except PageNotAnInteger:
        logs = paginator.page(1)  # If page is not an integer, show the first page
    except EmptyPage:
        logs = paginator.page(paginator.num_pages)  # If page is out of range, show the last page

    return render(request, 'repairs/activity_logs.html', {'logs': logs})

@login_required
def global_search(request):
    query = request.GET.get('q', '').strip()
    if not query:
        return JsonResponse({"results": []})

    # Search in different tables
    customer_results = Customer.objects.filter(
        Q(first_name__icontains=query) |
        Q(last_name__icontains=query) |
        Q(email__icontains=query) |
        Q(phone__icontains=query) |
        Q(postcode__icontains=query)
    ).values('id', 'first_name', 'last_name', 'email', 'phone', 'postcode')

    repair_results = Repair.objects.filter(
        Q(customer__first_name__icontains=query) |
        Q(customer__last_name__icontains=query) |
        Q(customer__email__icontains=query) |
        Q(customer__phone__icontains=query) |
        Q(customer__postcode__icontains=query) |
        Q(passcode__icontains=query) |
        Q(model__icontains=query) |
        Q(imei__icontains=query) |
        Q(device_type__icontains=query) |
        Q(repair_number__icontains=query)
    ).values(
        'id', 'repair_number', 'model', 'imei', 'device_type',
        'customer__first_name', 'customer__last_name', 'status__name', 'make__name', 'status__name',
        'location__name', 'finalized_price', 'estimated_cost', 'lens_lcd_damage', 'camera_lens_back_damage',
        'camera_lens_back_damage', 'risk_back', 'risk_biometric', 'button_function_ok', 'sim_removed', 'risk_lcd',
        'trackpad_functional', 'keyboard_functional', 'hinge_damage', 'screen_damage', 'liquid_damage', 'power_up',
        'missing_part', 'tampered', 'operating_system', 'ram', 'storage', 'network', 'passcode', 'issue_description', 'technicianNotes__notes'
    )

    make_results = Make.objects.filter(
        Q(name__icontains=query)
    ).values('id', 'name')

    status_results = RepairStatus.objects.filter(
        Q(name__icontains=query)
    ).values('id', 'name', 'description')

    location_results = Location.objects.filter(
        Q(name__icontains=query)
    ).values('id', 'name', 'address')

    # Combine all results
    results = {
        "customers": list(customer_results),
        "repairs": list(repair_results),
        "makes": list(make_results),
        "statuses": list(status_results),
        "locations": list(location_results),
    }

    return JsonResponse({"results": results})

@csrf_exempt
def add_customer(request):
    if request.method == 'POST':
        print("request.body", request.body)
        try:
            # Parse the incoming JSON data
            first_name = request.POST.get('first_name', '').strip()
            last_name = request.POST.get('last_name', '').strip()
            email = request.POST.get('email', '').strip()
            phone = request.POST.get('phone', '').strip()
            postcode = request.POST.get('postcode', '').strip()

            if not first_name or not last_name:
                return JsonResponse({'success': False, 'error': 'First name and last name are required.'}, status=400)

            # Save customer in the database
            customer = Customer(first_name=first_name, last_name=last_name, phone=phone)
            if email:
                customer.email = email
            if postcode:
                customer.postcode = postcode
            customer.save()
            # Return success response with customer details
            return JsonResponse({
                'success': True,
                'customer': {
                    'id': customer.id,
                    'first_name': customer.first_name,
                    'last_name': customer.last_name,
                }
            })

        except json.JSONDecodeError:
            return JsonResponse({'success': False, 'error': 'Invalid JSON data.'}, status=400)
        except Exception as e:
            return JsonResponse({'success': False, 'error': str(e)}, status=500)
    else:
        return JsonResponse({'success': False, 'error': 'Invalid request method.'}, status=405)


@csrf_exempt  # Use only if you need to bypass CSRF protection (ensure CSRF protection in production for security)
def save_notes(request):
    if request.method == 'POST':
        try:
            # Get data from the request body
            data = json.loads(request.body)
            repair_id = data.get('repair_id')
            location_id = data.get('location1', None)
            status_id = data.get('status', None)
            send_email = data.get('send_email', False)
            send_sms = data.get('send_sms', False)
            notes = data.get('notes', [])

            # Fetch the repair object by ID
            repair = Repair.objects.get(id=repair_id)

            # Get the current status and location for comparison
            previous_status = repair.status.name if repair.status else None
            previous_location = repair.location.name if repair.location else None

            # Update the status and location if provided
            new_status = get_object_or_404(RepairStatus, id=status_id) if status_id else None
            new_location = get_object_or_404(Location, id=location_id) if location_id else None
            repair.status = new_status
            repair.location = new_location
            repair.save()

            # Prepare log entry for changes in status and location
            changes = []
            if previous_status != (new_status.name if new_status else None):
                changes.append({
                    'field': 'Status',
                    'before': previous_status,
                    'after': new_status.name if new_status else 'N/A',
                    'time': str(datetime.datetime.now())
                })
            if previous_location != (new_location.name if new_location else None):
                changes.append({
                    'field': 'Location',
                    'before': previous_location,
                    'after': new_location.name if new_location else 'N/A',
                    'time': str(datetime.datetime.now())
                })

            # Log the notes
            if len(notes) > 0:
                if repair.technicianNotes:
                    existing_notes = repair.technicianNotes.notes
                    for note in notes:
                        existing_notes.append({
                            'note': note,
                            'time': str(datetime.datetime.now()),
                            'send_email': send_email,
                            'send_sms': send_sms,
                            'changes': changes
                        })
                    repair.technicianNotes.notes = existing_notes
                    repair.technicianNotes.save()
                else:
                    new_notes = [
                        {
                            'note': note,
                            'time': str(datetime.datetime.now()),
                            'send_email': send_email,
                            'send_sms': send_sms,
                            'changes': changes
                        }
                        for note in notes
                    ]
                    tech_notes = TechnicianNotes.objects.create(notes=new_notes)
                    repair.technicianNotes = tech_notes
                    repair.save()

            # Prepare data for email/SMS
            repair_data = {
                'repair_number': repair.repair_number,
                'device_type': repair.device_type,
                'status': repair.status.name if repair.status else 'N/A',
                'location': repair.location.name if repair.location else 'N/A',
                'technician_notes': repair.technicianNotes.notes if repair.technicianNotes else [],
                'changes': changes
            }

            # Send email if requested
            if send_email and repair.customer.email:
                subject = f"Repair Update: {repair.repair_number}"
                email_html_message = render_to_string('email_template.html', repair_data)
                email_plain_message = strip_tags(email_html_message)
                send_email_with_smtp_go(
                    subject,
                    email_plain_message,
                    email_html_message,
                    repair.customer.email,  # Recipient email
                )

            # Send SMS if requested
            if send_sms:
                account_sid = settings.TWILIO_ACCOUNT_SID  # Twilio SID from settings
                auth_token = settings.TWILIO_AUTH_TOKEN  # Twilio Auth Token from settings
                client = Client(account_sid, auth_token)
                sms_message = render_to_string('sms_template.txt', repair_data)
                try:
                    client.messages.create(
                        body=sms_message,
                        from_=settings.TWILIO_PHONE_NUMBER,  # Twilio phone number
                        to=repair.customer.phone  # Customer phone number
                    )
                except Exception as sms_error:
                    print(f"Twilio Error: {sms_error}")

            # Return a success response
            return JsonResponse({'success': True, 'changes': changes})
        except Repair.DoesNotExist:
            return JsonResponse({'success': False, 'error': 'Repair not found.'})
        except Exception as e:
            return JsonResponse({'success': False, 'error': str(e)})

    return JsonResponse({'success': False, 'error': 'Invalid request method.'})