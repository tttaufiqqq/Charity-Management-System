# Analytics Dashboard Charts Guide

## Overview

The admin analytics dashboard at `/admin/analytics` now features **15 informative charts and visualizations** across 6 different tabs, all powered by complex SQL queries with JOIN operations.

---

## Charts by Tab

### 📊 Overview Tab (6 Charts)

#### 1. **Donations Over Time** (Line Chart)
- **Query Type**: Time-series aggregation
- **Data Source**: `donation` table
- **Visualization**: Line chart with area fill
- **Insights**: Daily donation trends, peak donation periods
- **Color**: Green gradient

#### 2. **User Growth** (Line Chart)
- **Query Type**: Time-series aggregation by created_at
- **Data Source**: `users` table
- **Visualization**: Line chart
- **Insights**: Platform growth, user acquisition trends
- **Color**: Indigo

#### 3. **Campaign Status Distribution** (Doughnut Chart)
- **Query Type**: GROUP BY Status
- **Data Source**: `campaign` table
- **Visualization**: Doughnut chart
- **Insights**: Active vs Pending vs Completed campaigns
- **Colors**: Green (Active), Yellow (Pending), Blue (Completed)

#### 4. **Payment Methods** (Doughnut Chart)
- **Query Type**: GROUP BY Payment_Method with SUM(Amount)
- **Data Source**: `donation` table
- **Visualization**: Doughnut chart
- **Insights**: Preferred payment channels, transaction distribution
- **Colors**: Indigo (Online Banking), Orange (Credit/Debit), Purple (E-Wallet), Pink (Other)

#### 5. **Top States by Fundraising** (Horizontal Bar Chart)
- **Query Type**: Complex JOIN - `organization` + `campaign`
- **SQL Features**:
  ```sql
  LEFT JOIN campaign ON organization.Organization_ID = campaign.Organization_ID
  GROUP BY State, City
  ORDER BY total_raised DESC
  ```
- **Visualization**: Horizontal bar chart (top 10 states)
- **Insights**: Geographic distribution of fundraising success
- **Color**: Blue

#### 6. **Fund Allocation Overview** (Doughnut Chart)
- **Query Type**: Complex JOIN - `campaign` + `donation_allocation`
- **SQL Features**:
  ```sql
  LEFT JOIN donation_allocation ON campaign.Campaign_ID = donation_allocation.Campaign_ID
  COALESCE(SUM(Amount_Allocated), 0)
  ```
- **Visualization**: Doughnut chart showing allocated vs unallocated
- **Insights**: Fund distribution efficiency
- **Colors**: Green (Allocated), Yellow (Unallocated)

#### 7. **Campaign Success Funnel** (Bar Chart)
- **Query Type**: Aggregation with CASE statements
- **Data Source**: `campaign` table
- **Visualization**: Bar chart showing campaign pipeline
- **Insights**: Total → Active → Successful → Pending
- **Colors**: Gray (Total), Blue (Active), Green (Successful), Yellow (Pending)

---

### 🎯 Campaigns Tab (2 Charts + Table)

#### 8. **Top 10 Campaigns by Amount Raised** (Horizontal Bar Chart)
- **Query Type**: Complex 3-table JOIN
- **SQL Features**:
  ```sql
  JOIN organization ON campaign.Organization_ID = organization.Organization_ID
  JOIN users ON organization.Organizer_ID = users.id
  LEFT JOIN donation ON campaign.Campaign_ID = donation.Campaign_ID
  GROUP BY campaign.Campaign_ID
  COUNT(DISTINCT donation.Donation_ID) as donation_count
  COUNT(DISTINCT donation.Donor_ID) as donor_count
  ```
- **Visualization**: Horizontal bar chart
- **Insights**: Best performing campaigns, fundraising leaders
- **Color**: Green

#### 9. **Campaign Achievement Rates** (Table with Progress Bars)
- **Data Display**: Interactive table with:
  - Campaign name and organizer
  - Amount raised vs goal
  - Achievement percentage with visual progress bar
  - Donor count and donation count
- **Color Coding**: Green progress bars

---

### 🏢 Organizations Tab (2 Charts + Table)

#### 10. **Top 10 Organizations Performance** (Horizontal Bar Chart)
- **Query Type**: Multi-table JOIN with aggregations
- **SQL Features**:
  ```sql
  JOIN users ON organization.Organizer_ID = users.id
  LEFT JOIN campaign ON organization.Organization_ID = campaign.Organization_ID
  LEFT JOIN event ON organization.Organization_ID = event.Organizer_ID
  SUM(campaign.Collected_Amount) as total_raised
  COUNT(DISTINCT campaign.Campaign_ID) as total_campaigns
  COUNT(DISTINCT event.Event_ID) as total_events
  ```
- **Visualization**: Horizontal bar chart
- **Insights**: Organization fundraising leaderboard
- **Color**: Purple

#### 11. **Organization Leaderboard** (Table with Rankings)
- **Data Display**:
  - Rank badges (Gold/Silver/Bronze for top 3)
  - Total raised, campaigns, events, active campaigns
  - Location (City, State)
- **Features**: Visual ranking with colored badges

---

### 💖 Donors Tab (2 Charts + Table)

#### 12. **Top 10 Donors by Total Contribution** (Horizontal Bar Chart)
- **Query Type**: Complex JOIN with aggregations
- **SQL Features**:
  ```sql
  JOIN donor ON donation.Donor_ID = donor.Donor_ID
  JOIN users ON donor.User_ID = users.id
  SUM(donation.Amount) as total_donated
  AVG(donation.Amount) as avg_donation
  COUNT(DISTINCT donation.Campaign_ID) as campaigns_supported
  ```
- **Visualization**: Horizontal bar chart
- **Insights**: Most generous donors, contribution patterns
- **Color**: Pink

#### 13. **Donor Insights Table** (Enhanced Table)
- **Data Display**:
  - Total donated amount
  - Number of donations
  - Average donation
  - Campaigns supported
  - First and last donation dates
- **Features**: Donor loyalty analysis, retention insights

---

### 📅 Events Tab (2 Charts + Table)

#### 14. **Event Fill Rates & Volunteer Hours** (Dual-Axis Bar Chart)
- **Query Type**: Complex JOIN with participation data
- **SQL Features**:
  ```sql
  JOIN organization ON event.Organizer_ID = organization.Organization_ID
  LEFT JOIN event_participation ON event.Event_ID = event_participation.Event_ID
  COUNT(DISTINCT event_participation.Volunteer_ID) as volunteers_registered
  SUM(event_participation.Total_Hours) as total_hours
  ROUND((volunteers_registered / NULLIF(event.Capacity, 0)) * 100, 2) as fill_rate
  ```
- **Visualization**: Dual-axis bar chart
  - Left axis: Fill Rate (%)
  - Right axis: Total Hours
- **Insights**: Event capacity utilization, volunteer engagement
- **Colors**: Blue (Fill Rate), Purple (Hours)

#### 15. **Event Participation Metrics Table** (Enhanced Table)
- **Data Display**:
  - Event status with color-coded badges
  - Capacity vs registered volunteers
  - Fill rate with progress bars
  - Total volunteer hours contributed
- **Features**: Real-time participation tracking

---

### 🌍 Geography Tab (Table Only)

#### 16. **Geographic Distribution Table** (Data Table)
- **Query Type**: Aggregation by location
- **Data Display**:
  - State and City
  - Organization count
  - Campaign count
  - Total raised per location
- **Insights**: Regional fundraising performance

---

## Technical Implementation

### Database-Agnostic Queries

All charts use the `$quotedColumn` helper for cross-database compatibility:

```php
$quotedColumn = function ($table, $column) {
    $driver = DB::connection()->getDriverName();
    if ($driver === 'pgsql') {
        return "\"{$table}\".\"{$column}\"";
    }
    return "`{$table}`.`{$column}`";
};
```

**Works on:**
- ✅ PostgreSQL
- ✅ MySQL
- ✅ MariaDB

### Chart.js Configuration

**Features:**
- Responsive design
- Currency formatting (RM with 2 decimal places)
- Custom tooltips
- Color-coded datasets
- Auto-refresh on Livewire updates
- Smooth animations

**Chart Types Used:**
- Line Charts (2)
- Doughnut Charts (4)
- Bar Charts (5)
- Horizontal Bar Charts (4)
- Dual-Axis Charts (1)

---

## Key Insights Provided

### Financial Insights
1. **Total fundraising performance** across the platform
2. **Fund allocation efficiency** (raised vs allocated)
3. **Payment method preferences** among donors
4. **Geographic fundraising distribution**

### Campaign Analytics
1. **Top performing campaigns** with donor engagement
2. **Campaign success rates** (goal achievement)
3. **Campaign status pipeline** (funnel analysis)
4. **Achievement percentages** per campaign

### Organization Metrics
1. **Organization leaderboard** by total raised
2. **Multi-metric performance** (campaigns, events, active status)
3. **Geographic presence** by state/city

### Donor Behavior
1. **Top donor contributions** and patterns
2. **Average donation amounts** per donor
3. **Campaign diversity** (how many campaigns supported)
4. **Donor retention** (first vs last donation dates)

### Event Effectiveness
1. **Volunteer participation rates** (fill rates)
2. **Total volunteer hours** contributed
3. **Event capacity utilization**
4. **Status-based filtering** (Upcoming, Ongoing, Completed)

### Growth Metrics
1. **User acquisition trends** over time
2. **Daily donation patterns**
3. **Platform activity** trends

---

## Usage Tips

### Filtering by Date Range
The dashboard supports 4 date ranges:
- **Last 7 Days** - Recent activity
- **Last 30 Days** (Default) - Monthly overview
- **Last 90 Days** - Quarterly trends
- **Last Year** - Annual analysis

### Tab Navigation
- **Overview**: Executive summary with key metrics
- **Campaigns**: Deep dive into campaign performance
- **Organizations**: Leaderboard and rankings
- **Donors**: Donor insights and patterns
- **Events**: Volunteer engagement metrics
- **Geography**: Regional distribution

### Interactive Features
- ✅ Hover tooltips with detailed information
- ✅ Auto-refresh when date range changes
- ✅ Livewire reactive updates
- ✅ Responsive charts (mobile-friendly)
- ✅ Color-coded for easy interpretation

---

## Performance Optimization

### Query Optimization
1. **Indexed columns** used in JOIN conditions
2. **DISTINCT counts** for accurate metrics
3. **LIMIT clauses** on all top-N queries
4. **Eager loading** with Livewire wire:ignore

### Frontend Optimization
1. **Chart.js CDN** for fast loading
2. **Lazy chart initialization** (only when visible)
3. **Chart destruction** before re-rendering
4. **queueMicrotask** for smooth re-renders

---

## Summary

The analytics dashboard now provides:

✅ **15 Charts** across 6 tabs
✅ **Complex SQL Queries** with JOINs, aggregations, and CASE statements
✅ **Cross-Database Compatibility** (PostgreSQL, MySQL, MariaDB)
✅ **Real-Time Updates** with Livewire
✅ **Professional Visualizations** with Chart.js
✅ **Actionable Insights** for decision-making

The dashboard transforms raw data from 10+ database tables into beautiful, informative visualizations that help administrators understand platform performance at a glance.
