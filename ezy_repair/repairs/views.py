from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.decorators import login_required
from .models import Repair, Location, Make, RepairStatus, ActivityLog

# Repairs
@login_required
def repairs_list(request):
    repairs = Repair.objects.all()
    return render(request, 'repairs/repairs_list.html', {'repairs': repairs})

@login_required
def add_repair(request):
    if request.method == "POST":
        # Handle repair creation logic
        pass
    return redirect('repairs_list')

@login_required
def edit_repair(request, pk):
    repair = get_object_or_404(Repair, pk=pk)
    if request.method == "POST":
        # Handle repair editing logic
        pass
    return redirect('repairs_list')

@login_required
def delete_repair(request, pk):
    repair = get_object_or_404(Repair, pk=pk)
    repair.delete()
    return redirect('repairs_list')

# Locations
@login_required
def locations(request):
    locations = Location.objects.all()
    return render(request, 'repairs/locations.html', {'locations': locations})

# Makes
@login_required
def makes(request):
    makes = Make.objects.all()
    return render(request, 'repairs/makes.html', {'makes': makes})

# Repair Statuses
@login_required
def repair_statuses(request):
    statuses = RepairStatus.objects.all()
    return render(request, 'repairs/repair_statuses.html', {'statuses': statuses})

# Activity Logs
@login_required
def activity_logs(request):
    logs = ActivityLog.objects.all()
    return render(request, 'repairs/activity_logs.html', {'logs': logs})
